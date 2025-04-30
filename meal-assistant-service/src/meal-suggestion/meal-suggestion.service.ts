import { Injectable } from '@nestjs/common';
import { InjectModel } from '@nestjs/mongoose';
import { Model } from 'mongoose';
import { MealSuggestion, MealSuggestionFood } from './schemas/meal-suggestion.schema';
import { CreateMealSuggestionDto } from './dto/create-meal-suggestion.dto';
import { OpenAI } from 'openai';

const openai = new OpenAI({
  apiKey: process.env.OPENAI_API_KEY,
});
interface UserData {
  weight: number;
  height: number;
  age: number;
  gender: 'male' | 'female';
  caloriesConsumedToday: number;
}


@Injectable()
export class MealSuggestionService {
  constructor(
    @InjectModel(MealSuggestion.name)
    private mealSuggestionModel: Model<MealSuggestion>,
    @InjectModel(MealSuggestionFood.name)
    private mealSuggestionFoodModel: Model<MealSuggestionFood>,
  ) {}

  async createSuggestion(dto: CreateMealSuggestionDto): Promise<MealSuggestion> {
    const aiSuggestion = await this.generateAiSuggestion(dto.userData, dto.goals);

    const mealSuggestion = new this.mealSuggestionModel({
      aiAgent: new Date(),
      mealSuggestion: aiSuggestion.meal,
      status: 'pending',
      is_accepted: false,
      foods: [],
    });

    const savedSuggestion = await mealSuggestion.save();

    const foods = await Promise.all(
      aiSuggestion.foods.map((food) =>
        new this.mealSuggestionFoodModel({
          aliment_id: food.aliment_id,
          quantite: food.quantite,
          unit: food.unit,
          mealSuggestion: savedSuggestion._id,
        }).save(),
      ),
    );

    savedSuggestion.foods = foods.map((food) => food._id);
    await savedSuggestion.save();

    return savedSuggestion;
  }

  private async generateAiSuggestion(userData: UserData, goals: string[]): Promise<any> {
    const { weight, height, age, gender, caloriesConsumedToday } = userData;
  
    const bmr =
      gender === 'male'
        ? 10 * weight + 6.25 * height - 5 * age + 5
        : 10 * weight + 6.25 * height - 5 * age - 161;
  
    const goal = goals.includes('weight_loss')
      ? 'weight_loss'
      : goals.includes('muscle_gain')
      ? 'muscle_gain'
      : 'balanced';
  
    let targetCalories = bmr;
  
    if (goal === 'weight_loss') {
      targetCalories -= 500;
    } else if (goal === 'muscle_gain') {
      targetCalories += 300;
    }
  
    const remainingCalories = targetCalories - caloriesConsumedToday;
  
    let mealSuggestion;
    if (goal === 'weight_loss') {
      mealSuggestion = {
        name: 'Grilled Salmon with Quinoa & Veggies',
        type: 'low_calorie',
        description: 'A light but filling dish rich in protein and fiber',
        foods: [
          { name: 'Grilled Salmon', quantity: 120, unit: 'g' },
          { name: 'Quinoa', quantity: 80, unit: 'g' },
          { name: 'Steamed Broccoli', quantity: 100, unit: 'g' },
        ],
      };
    } else if (goal === 'muscle_gain') {
      mealSuggestion = {
        name: 'Chicken Pasta with Avocado',
        type: 'high_protein',
        description: 'A protein-packed meal to support muscle growth',
        foods: [
          { name: 'Chicken Breast', quantity: 150, unit: 'g' },
          { name: 'Whole Wheat Pasta', quantity: 100, unit: 'g' },
          { name: 'Avocado', quantity: 50, unit: 'g' },
        ],
      };
    } else {
      mealSuggestion = {
        name: 'Turkey Sandwich with Mixed Greens',
        type: 'balanced',
        description: 'A balanced meal for daily maintenance',
        foods: [
          { name: 'Whole Wheat Bread', quantity: 2, unit: 'slices' },
          { name: 'Turkey Slices', quantity: 100, unit: 'g' },
          { name: 'Lettuce & Tomato', quantity: 50, unit: 'g' },
        ],
      };
    }
  
    return {
      meal: {
        name: mealSuggestion.name,
        type: mealSuggestion.type,
        description: mealSuggestion.description,
        remainingCalories,
      },
      foods: mealSuggestion.foods,
    };
  }
  
}