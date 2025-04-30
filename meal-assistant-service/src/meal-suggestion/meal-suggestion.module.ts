import { Module } from '@nestjs/common';
import { MongooseModule } from '@nestjs/mongoose';
import { MealSuggestion, MealSuggestionSchema, MealSuggestionFood, MealSuggestionFoodSchema } from './schemas/meal-suggestion.schema';
import { MealSuggestionService } from './meal-suggestion.service';
import { MealSuggestionController } from './meal-suggestion.controller';

@Module({
  imports: [
    MongooseModule.forFeature([
      { name: MealSuggestion.name, schema: MealSuggestionSchema },
      { name: MealSuggestionFood.name, schema: MealSuggestionFoodSchema },
    ]),
  ],
  controllers: [MealSuggestionController],
  providers: [MealSuggestionService],
})
export class MealSuggestionModule {}