import { Prop, Schema, SchemaFactory } from '@nestjs/mongoose';
import { Document } from 'mongoose';

export type MealSuggestionDocument = MealSuggestion & Document;

@Schema()
export class MealSuggestion {

    @Prop({ required: false })
    aiAgent: string;

    @Prop({ required: true , type : Object})
    mealSuggestion: Record<string, any>;

    @Prop({ required: false })
    status : boolean
    
    @Prop({type: Boolean , required : false})
    is_accepted : boolean

    @Prop({ required: false })
    createAt: Date;
    
    @Prop({ required: false })
    updateAt: Date;

}

export const MealSuggestionSchema = SchemaFactory.createForClass(MealSuggestion);
