import { Injectable } from '@nestjs/common';
import { CreateMealSuggestionDto } from './dto/create-meal-suggestion.dto';
import { UpdateMealSuggestionDto } from './dto/update-meal-suggestion.dto';
import { Model } from 'mongoose';
import { MealSuggestion, MealSuggestionDocument } from './schemas/meal-suggestion.schema';
import { InjectModel } from '@nestjs/mongoose';


@Injectable()
export class MealSuggestionService {

  constructor(@InjectModel(MealSuggestion.name) private mealModel: Model<MealSuggestionDocument>) {}

  async create(createMealSuggestionDto: CreateMealSuggestionDto): Promise<MealSuggestion> {
    const newMeal = new this.mealModel(createMealSuggestionDto);
    return newMeal.save();

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
