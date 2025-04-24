<?php

namespace App\Repositories;

use App\Http\Resources\mealsTrends;
use App\Models\Meal;
use App\Repositories\Interfaces\MealRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MealRepository implements MealRepositoryInterface
{
    public function getAll()
    {
        return Meal::with('foods')->get();
    }

    public function getById($id)
    {
        return Meal::with('foods')->findOrFail($id);
    }

    public function create(array $data)
    {
        try {
            
            $dateFormatted = \Carbon\Carbon::parse($data["meal"]['date'])->format('Y-m-d');
            $meal = new Meal();
            $newmeal = [];
            $newmeal['user_id'] = $data["meal"]['user_id'];
            $newmeal['name'] = $data["meal"]['name'];
            $newmeal['meal_type'] = $data["meal"]['meal_type'];
            $newmeal['created_at'] = $dateFormatted . ' ' . \Carbon\Carbon::now()->format('H:i:s');
            
            $meal->forceFill($newmeal)->save();

            $foodData = [];
            foreach ($data['meal_items'] as $item) {
                $foodData[$item['id']] = [
                    'quantity' => $item['quantity'],
                    'unite' => $item['unite'],
                ];
            }

            $meal->foods()->attach($foodData);
            
            $totalNutrients = $this->calculateDayNutrition($meal->user_id, $dateFormatted);

            return $totalNutrients;

        } catch (\Exception $e) {
            return 'Error creating meal: ' . $e->getMessage();
        }

    }

    public function calculateDayNutrition($userId, $date)
    {  
        try{
        $meals = Meal::with('foods')
            ->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->get();

        $total = ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];

        foreach ($meals as $meal) {
            foreach ($meal->foods as $food) {
                $pivot = $food->pivot;
                $factor = $pivot->quantity;
                $total['calories'] += $food->calories * $factor;
                $total['protein'] += $food->proteins * $factor;
                $total['fat'] += $food->lipides * $factor;
            }
        }
        
        $goals = $this->fetchNutritionGoals($userId);
        Log::info('Calculating day nutrition for user ' . $userId . ' on date ' . $date);
        Log::info(
            "[goals protein : " . $goals['protein'] . " total protein : ". $total['protein'] . 
            "[goals carbs :" . $goals['carbs']  . " total calories : ". $total['calories']." ]" );
        
            if($goals['protein'] <= $total['protein'] && $goals['carbs'] <= $total['calories'] && $goals['fat'] <= $total['fat']) {
                Log::info('You reached your daily goal');
                $this->publishToRabbitMQ('nutrition.notification', [
                    "message" => "You reached your daily goal"]);
            }

        return $goals;
        }catch(\Exception $e) {
            return 'Error fetching nutrition goals: ' . $e->getMessage();
        }
    }
    
    private function fetchNutritionGoals($userId)
    {
        try{
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', env('NUTRITION_GOALS_API_URL') . '/api/nutritiongoeals?userId=' . $userId, [
                'headers' => [
                'Accept' => 'application/json',
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                $goals = json_decode($response->getBody(), true);
                return [
                    'protein' => $goals['proteinTarget'] ?? 0,
                    'carbs' => $goals['carbTarget'] ?? 0,
                    'fat' => $goals['fatTarget'] ?? 0
                ];
            }
            return [
                'protein' => 0,
                'carbs' => 0,
                'fat' => 0
            ];
            
        }catch(\Exception $e) {
            throw new \Exception('Error fetching nutrition goals: ' . $e->getMessage());
        }
    }

    public function publishToRabbitMQ($queue, $data)
    {
        try{
            $connection = new AMQPStreamConnection(
                    env('RABBITMQ_HOST', 'rabbitmq'),
                    env('RABBITMQ_PORT', 5672),
                    env('RABBITMQ_USER', 'user'),
                    env('RABBITMQ_PASSWORD', 'password'),
                    env('RABBITMQ_VHOST', '/')
                );
            $channel = $connection->channel();
        
            $channel->queue_declare($queue, false, true, false, false);
        
            $msg = new AMQPMessage(json_encode($data));
            $channel->basic_publish($msg, '', $queue);
        
            $channel->close();
            $connection->close();
        }catch(\Exception $e) {
            throw new \Exception('Error publishing to RabbitMQ: ' . $e->getMessage());
        }
    }
    
    public function getCaloroysTrendbyDate($date, $userId)
    {
        try {
            $parsedDate = Carbon::parse($date);
            $startOfWeek = $parsedDate->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $parsedDate->copy()->endOfWeek(Carbon::SUNDAY);
            
            return Meal::where('user_id', $userId)
                ->whereBetween('created_at', [
                    $startOfWeek->format('Y-m-d') . ' 00:00:00',
                    $endOfWeek->format('Y-m-d') . ' 23:59:59'
                ])
                ->with(['foods' => function($query) {
                    $query->withPivot('quantity','unite');
                }])
                ->orderBy('created_at')
                ->get();
        } catch (\Exception $e) {
            throw new \Exception('Error fetching meals: ' . $e->getMessage());
        }
    }

    // private function getMealType($date)
    // {
    //     $time = Carbon::parse($date);
    //     $ifExists = Meal::where('created_at', '>=', $time->copy()->startOfDay())
    //     ->where('created_at', '<=', $time->copy()->endOfDay());
    //     if($ifExists->exists()) {
    //         return "Snack";
    //     }
    //     if ($time->hour >= 5 && $time->hour < 11) {
    //         return 'Breakfast';
    //     } elseif ($time->hour >= 11 && $time->hour < 16) {
    //         return 'Lunch';
    //     } elseif ($time->hour >= 16 && $time->hour < 22) {
    //         return 'Dinner';
    //     } else {
    //         return 'Snack';
    //     }
    // }

    public function update(Meal $meal, array $data)
    {
        if (isset($data['meal_image'])) {
            if ($meal->meal_image) {
                Storage::disk('public')->delete($meal->meal_image);
            }
            $data['meal_image'] = $data['meal_image']->store('meals', 'public');
        }

        $meal->update($data);

        return $meal;
    }

    public function delete(Meal $meal)
    {
        if ($meal->meal_image) {
            Storage::disk('public')->delete($meal->meal_image);
        }

        $meal->delete();
    }
}
