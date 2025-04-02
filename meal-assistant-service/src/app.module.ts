import { Module } from '@nestjs/common';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { MealSuggestionModule } from './meal-suggestion/meal-suggestion.module';
import { MealSuggestionModule } from './meal-suggestion/meal-suggestion.module';

@Module({
  imports: [MealSuggestionModule],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
