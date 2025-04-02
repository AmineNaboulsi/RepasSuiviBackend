import { Module } from '@nestjs/common';
import { MealSuggestionService } from './meal-suggestion.service';
import { MealSuggestionController } from './meal-suggestion.controller';
import { MongooseModule } from '@nestjs/mongoose';
import { MealSuggestion, MealSuggestionSchema } from './schemas/meal-suggestion.schema';

@Module({
  imports: [MongooseModule.forFeature([{ name: MealSuggestion.name, schema: MealSuggestionSchema }])],
  controllers: [MealSuggestionController],
  providers: [MealSuggestionService],
})

export class MealSuggestionModule {}
