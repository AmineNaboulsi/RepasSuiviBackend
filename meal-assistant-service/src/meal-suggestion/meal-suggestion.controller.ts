import { Controller,Get, Post, Body } from '@nestjs/common';
import { MealSuggestionService } from './meal-suggestion.service';
import { CreateMealSuggestionDto } from './dto/create-meal-suggestion.dto';

@Controller('meal-suggestions')
export class MealSuggestionController {
  constructor(private readonly mealSuggestionService: MealSuggestionService) {}

  @Get()
  async hello() {
    return "hello"
  }
  @Post()
  async create(@Body() createMealSuggestionDto: CreateMealSuggestionDto) {
    return this.mealSuggestionService.createSuggestion(createMealSuggestionDto);
  }
}