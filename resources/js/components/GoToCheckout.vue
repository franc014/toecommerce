<template>
    <div>
        <Form action="/users" method="post">
            <input type="text" name="name" />
            <input type="email" name="email" />
            <button type="submit">Create User</button>
        </Form>
    </div>
</template>

<script setup lang="ts"></script>

<style scoped></style>
