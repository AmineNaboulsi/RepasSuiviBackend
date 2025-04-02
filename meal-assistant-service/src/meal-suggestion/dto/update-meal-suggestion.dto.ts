import { PartialType } from '@nestjs/mapped-types';
import { CreateMealSuggestionDto } from './create-meal-suggestion.dto';

export class UpdateMealSuggestionDto extends PartialType(CreateMealSuggestionDto) {}
