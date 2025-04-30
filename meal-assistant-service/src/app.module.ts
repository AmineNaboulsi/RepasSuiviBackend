import { Module } from '@nestjs/common';
import { MongooseModule } from '@nestjs/mongoose';
import { MealSuggestionModule } from './meal-suggestion/meal-suggestion.module';
import { ConfigModule } from '@nestjs/config';

@Module({
  imports: [
    ConfigModule.forRoot(),
    MongooseModule.forRoot(
      process.env.MONGODB_URI || 'mongodb://localhost:27017/meal-suggestion-db'),
      
    MealSuggestionModule,
  ],
})
export class AppModule {}