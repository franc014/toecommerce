<template>
    <AlertDialog>
        <AlertDialogTrigger as-child>
            <Button variant="outline">
                <slot name="icon"></slot>
                {{ buttonLabel }}
            </Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle> {{ title }} </AlertDialogTitle>
                <AlertDialogDescription> {{ description }} </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>{{ cancelLabelAction }}</AlertDialogCancel>
                <AlertDialogAction @click="handleAction">{{ acceptLabelAction }}</AlertDialogAction>
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
