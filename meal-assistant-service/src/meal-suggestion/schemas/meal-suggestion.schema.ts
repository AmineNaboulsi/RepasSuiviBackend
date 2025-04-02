import { Prop, Schema, SchemaFactory } from '@nestjs/mongoose';
import { Document } from 'mongoose';

export type MealSuggestionDocument = MealSuggestion & Document;

@Schema()
export class MealSuggestion {

    @Prop({ required: true })
    name: object;

    @Prop()
    mealSuggestion: object;

    @Prop()
    status : boolean

    @Prop()
    is_accepted : boolean
    
    @Prop({ required: true })
    image: object;

    @Prop({ required: false })
    createAt: Date;
    
    @Prop({ required: false })
    updateAt: Date;

}

export const MealSuggestionSchema = SchemaFactory.createForClass(MealSuggestion);
