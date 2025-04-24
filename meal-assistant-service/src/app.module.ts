import { Module } from '@nestjs/common';
import { MongooseModule } from '@nestjs/mongoose';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { ConfigModule } from '@nestjs/config';
import { MealSuggestionModule } from './meal-suggestion/meal-suggestion.module';
@Module({
    imports: [
      ConfigModule.forRoot({ isGlobal: true }),
      MongooseModule.forRoot(process.env.MONGODB_URI || 'mongodb://localhost:27017/meal-assistant'),
      MealSuggestionModule],
      
    controllers: [AppController  ],
    providers: [AppService],
})
export class AppModule {}
