import { Injectable } from '@nestjs/common';
import { CreateMealSuggestionDto } from './dto/create-meal-suggestion.dto';
import { UpdateMealSuggestionDto } from './dto/update-meal-suggestion.dto';

@Injectable()
export class MealSuggestionService {
  create(createMealSuggestionDto: CreateMealSuggestionDto) {
    return 'This action adds a new mealSuggestion';
  }

  findAll() {
    return `This action returns all mealSuggestion`;
  }

  findOne(id: number) {
    return `This action returns a #${id} mealSuggestion`;
  }

  update(id: number, updateMealSuggestionDto: UpdateMealSuggestionDto) {
    return `This action updates a #${id} mealSuggestion`;
  }

  remove(id: number) {
    return `This action removes a #${id} mealSuggestion`;
  }
}
