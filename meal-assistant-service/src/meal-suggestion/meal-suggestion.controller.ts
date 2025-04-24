import { Controller, Get, Post, Body, Patch, Param, Delete } from '@nestjs/common';
import { MealSuggestionService } from './meal-suggestion.service';
import { CreateMealSuggestionDto } from './dto/create-meal-suggestion.dto';
import { UpdateMealSuggestionDto } from './dto/update-meal-suggestion.dto';

@Controller('meal-suggestion')
export class MealSuggestionController {
  constructor(private readonly mealSuggestionService: MealSuggestionService) {}

  @Post()
  create(@Body() createMealSuggestionDto: CreateMealSuggestionDto) {
    return this.mealSuggestionService.create(createMealSuggestionDto);
  }

  @Get()
  findAll() {
    return this.mealSuggestionService.findAll();
  }

  @Get(':id')
  findOne(@Param('id') id: string) {
    return this.mealSuggestionService.findOne(+id);
  }

  @Patch(':id')
  update(@Param('id') id: string, @Body() updateMealSuggestionDto: UpdateMealSuggestionDto) {
    return this.mealSuggestionService.update(+id, updateMealSuggestionDto);
  }

  @Delete(':id')
  remove(@Param('id') id: string) {
    return this.mealSuggestionService.remove(+id);
  }
}
