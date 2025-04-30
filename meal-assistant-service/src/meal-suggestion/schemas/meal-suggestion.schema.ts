import { Prop, Schema, SchemaFactory } from '@nestjs/mongoose';
import { Document, Types } from 'mongoose';

@Schema({ timestamps: { createdAt: 'createdAt' } })
export class MealSuggestion extends Document {
  @Prop({ required: true })
  aiAgent: Date;

  @Prop({ type: Object, required: true })
  mealSuggestion: object;

  @Prop({ required: true })
  status: string;

  @Prop({ default: false })
  is_accepted: boolean;

  @Prop()
  createdAt: Date;

  @Prop([{ type: Types.ObjectId, ref: 'MealSuggestionFood' }])
  foods: Types.ObjectId[];
}

export const MealSuggestionSchema = SchemaFactory.createForClass(MealSuggestion);

@Schema()
export class MealSuggestionFood extends Document {
  @Prop({ required: true })
  aliment_id: number;

  @Prop({ required: true })
  quantite: number;

  @Prop({ required: true })
  unit: string;

  @Prop({ type: Types.ObjectId, ref: 'MealSuggestion' })
  mealSuggestion: Types.ObjectId;
}

export const MealSuggestionFoodSchema = SchemaFactory.createForClass(MealSuggestionFood);