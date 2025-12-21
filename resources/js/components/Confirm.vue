<template>
    <AlertDialog>
        <AlertDialogTrigger as-child>
            <Button variant="outline" class="hover:bg-orange-200">
                <slot name="icon"></slot>
                {{ buttonLabel }}
            </Button>
        </AlertDialogTrigger>
        <AlertDialogContent class="bg-orange-100">
            <AlertDialogHeader>
                <AlertDialogTitle> {{ title }} </AlertDialogTitle>
                <AlertDialogDescription> {{ description }} </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel class="hover:bg-orange-200">{{ cancelLabelAction }}</AlertDialogCancel>
                <AlertDialogAction class="bg-orange-800 text-zinc-100 hover:bg-orange-400 hover:text-zinc-600" @click="handleAction">{{
                    acceptLabelAction
                }}</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';

defineProps<{
    handleAction: Function;
    buttonLabel: string;
    cancelLabelAction: string;
    acceptLabelAction: string;
    title: string;
    description: string;
}>();
</script>

<style scoped></style>
