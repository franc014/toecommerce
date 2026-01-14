<template>
    <AppHead :metaTags="metaTags" :company="company" />
    <section class="contact-v3 relative z-[1] mt-50 md:mt-20">
        <div class="mx-auto mb-10 w-[calc(100%_-_2.5rem)] max-w-xl lg:mb-12 lg:w-[calc(100%_-_4rem)]">
            <div class="text-center">
                <h1 class="text-4x mb-2 font-serif2">{{ heading }}</h1>
                <p class="text-xl tracking-wider">{{ message }}</p>
            </div>
        </div>

        <div class="mx-auto w-[calc(100%_-_2.5rem)] max-w-lg md:max-w-3xl lg:w-[calc(100%_-_4rem)] lg:max-w-5xl">
            <div class="mb-20 grid grid-cols-12 gap-8 border-b border-dashed border-gray-300 pb-20 lg:gap-12">
                <div class="col-span-12 lg:col-span-6">
                    <CompanyInfo :company="company" />
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <ContactForm />
                </div>
            </div>
            <div>
                <Socials :socials="socials" />
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import AppHead from '@/components/AppHead.vue';
import CompanyInfo from '@/components/contact/CompanyInfo.vue';
import ContactForm from '@/components/contact/ContactForm.vue';
import Socials from '@/components/contact/Socials.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Company, Metatags, PageComponentContent, PageComponents } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const metaTags = page.props.metatags as Metatags;
const company = page.props.company as Company;
const components = page.props.components as PageComponents;

const socialMedia = company.socialMedia;

const facebook = socialMedia.facebook || '';
const instagram = socialMedia.instagram || '';
const twitter = socialMedia.twitter || '';
const youtube = socialMedia.youtube || '';

const socials = {
    facebook,
    instagram,
    twitter,
    youtube,
};

const contactIntro = computed(() => {
    return components['ContactIntro'].content as PageComponentContent;
});

const heading = computed(() => {
    return contactIntro.value.heading[0].content;
});
const message = computed(() => {
    return contactIntro.value.paragraph[0].content;
});
</script>

<style scoped></style>
