<template>
    <section class="feature-v4">
        <div class="mx-auto w-[calc(100%_-_2.5rem)] max-w-lg md:max-w-3xl lg:w-[calc(100%_-_4rem)] lg:max-w-5xl xl:max-w-7xl">
            <div class="feature-v4__grid grid grid-cols-12 items-center gap-5 lg:gap-8">
                <div class="relative z-[1] col-span-12 lg:col-span-5">
                    <div class="mb-1.5 text-sm text-gray-500 lg:mb-2 lg:text-base">This is a Label</div>

                    <div>
                        <h1 class="feature-v4__text-offset@md mb-2 text-5xl">Lorem ipsum dolor sit, amet consectetur adipisicing elit.</h1>
                        <p class="text-lg">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa, rem neque doloribus impedit explicabo minus laborum
                            cupiditate atque esse consectetur.
                        </p>
                    </div>

                    <div class="mt-3 lg:mt-5">
                        <div class="flex flex-wrap items-center gap-3 lg:gap-5">
                            <a
                                href="#0"
                                class="relative inline-flex cursor-pointer items-center justify-center rounded-md bg-indigo-700 px-4 py-2 text-[1em] leading-tight whitespace-nowrap text-white no-underline shadow-md transition-all duration-200 hover:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-700"
                                >Download</a
                            >
                            <a href="#0" class="text-inherit">Learn more</a>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-7">
                    <figure>
                        <img
                            class="block h-screen w-full object-cover"
                            src="https://images.unsplash.com/photo-1548624313-0396c75e4b1a?q=80&w=1587&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                            alt="Image description"
                        />
                    </figure>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts"></script>

<style scoped></style>
