<template>
    <footer class="footer-v6 section-spacing border-t border-zinc-300 bg-zinc-50/10">
        <div class="wrapper">
            <div class="mb-8 border-b border-dashed border-zinc-900/40 py-8 lg:mb-20">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between lg:gap-3">
                    <div><h2 class="font-serif2">Logo</h2></div>
                    <!-- <div>
                        <a
                            class="relative inline-flex cursor-pointer items-center justify-center rounded-md bg-orange-700 px-4 py-2 text-[1em] leading-tight whitespace-nowrap text-white no-underline shadow-md transition-all duration-200 hover:bg-orange-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700"
                            href="#0"
                            >Ven a visitarnos</a
                        >
                    </div> -->
                </div>
            </div>

            <div class="mb-12 lg:mb-20">
                <div class="grid grid-cols-12 gap-y-8 sm:gap-8 lg:gap-12">
                    <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                        <div class="mb-8 lg:mb-12">
                            <h4 class="mb-2 lg:mb-3 lg:text-xl">Menu</h4>
                            <ul class="grid grid-cols-12 gap-2 lg:gap-3 lg:text-base">
                                <li class="col-span-12" v-for="item in footerMenu.items" :key="item.id">
                                    <Link class="footer-v6__link" :href="item.url">{{ item.label }}</Link>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="mb-2 lg:mb-3 lg:text-xl">Legal</h4>
                            <ul class="grid grid-cols-12 gap-2 lg:gap-3 lg:text-base">
                                <li class="col-span-12" v-for="item in legalMenu.items" :key="item.id">
                                    <Link class="footer-v6__link" :href="item.url">{{ item.label }}</Link>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                        <h4 class="mb-2 lg:mb-3 lg:text-xl">Horarios de atención</h4>
                        <dl class="grid max-w-xs grid-cols-12 gap-2 text-zinc-500 lg:gap-3 lg:text-base xl:w-[70%]">
                            <div class="col-span-12 flex items-baseline justify-between" v-for="(value, day) in company.workingDays" :key="day">
                                <dt>{{ day }}</dt>
                                <dd class="ml-1.5 lg:ml-2">{{ value }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                        <h4 class="mb-2 lg:mb-3 lg:text-xl">Conéctate</h4>
                        <ul class="grid grid-cols-12 gap-2 lg:gap-3 lg:text-base">
                            <li class="col-span-12">
                                <a class="footer-v6__link inline-flex items-center" :href="instagram">
                                    <span class="footer-v6__link-icon" aria-hidden="true">
                                        <svg
                                            class="icon inline-block h-[1em] w-[1em] shrink-0 fill-current leading-none text-inherit"
                                            viewBox="0 0 16 16"
                                        >
                                            <g>
                                                <circle cx="12.145" cy="3.892" r="0.96"></circle>
                                                <path
                                                    d="M8,12c-2.206,0-4-1.794-4-4s1.794-4,4-4s4,1.794,4,4S10.206,12,8,12z M8,6C6.897,6,6,6.897,6,8 s0.897,2,2,2s2-0.897,2-2S9.103,6,8,6z"
                                                ></path>
                                                <path
                                                    d="M12,16H4c-2.056,0-4-1.944-4-4V4c0-2.056,1.944-4,4-4h8c2.056,0,4,1.944,4,4v8C16,14.056,14.056,16,12,16z M4,2C3.065,2,2,3.065,2,4v8c0,0.953,1.047,2,2,2h8c0.935,0,2-1.065,2-2V4c0-0.935-1.065-2-2-2H4z"
                                                ></path>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="ml-1 lg:ml-1.5">Instagram</span>
                                </a>
                            </li>

                            <li class="col-span-12">
                                <a class="footer-v6__link inline-flex items-center" :href="twitter">
                                    <span class="footer-v6__link-icon" aria-hidden="true">
                                        <svg
                                            class="icon inline-block h-[1em] w-[1em] shrink-0 fill-current leading-none text-inherit"
                                            viewBox="0 0 16 16"
                                        >
                                            <g>
                                                <path
                                                    d="M16,3c-0.6,0.3-1.2,0.4-1.9,0.5c0.7-0.4,1.2-1,1.4-1.8c-0.6,0.4-1.3,0.6-2.1,0.8c-0.6-0.6-1.5-1-2.4-1 C9.3,1.5,7.8,3,7.8,4.8c0,0.3,0,0.5,0.1,0.7C5.2,5.4,2.7,4.1,1.1,2.1c-0.3,0.5-0.4,1-0.4,1.7c0,1.1,0.6,2.1,1.5,2.7 c-0.5,0-1-0.2-1.5-0.4c0,0,0,0,0,0c0,1.6,1.1,2.9,2.6,3.2C3,9.4,2.7,9.4,2.4,9.4c-0.2,0-0.4,0-0.6-0.1c0.4,1.3,1.6,2.3,3.1,2.3 c-1.1,0.9-2.5,1.4-4.1,1.4c-0.3,0-0.5,0-0.8,0c1.5,0.9,3.2,1.5,5,1.5c6,0,9.3-5,9.3-9.3c0-0.1,0-0.3,0-0.4C15,4.3,15.6,3.7,16,3z"
                                                ></path>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="ml-1 lg:ml-1.5">Twitter</span>
                                </a>
                            </li>

                            <li class="col-span-12">
                                <a class="footer-v6__link inline-flex items-center" :href="youtube">
                                    <span class="footer-v6__link-icon" aria-hidden="true">
                                        <svg
                                            class="icon inline-block h-[1em] w-[1em] shrink-0 fill-current leading-none text-inherit"
                                            viewBox="0 0 16 16"
                                        >
                                            <g>
                                                <path
                                                    d="M15.8,4.8c-0.2-1.3-0.8-2.2-2.2-2.4C11.4,2,8,2,8,2S4.6,2,2.4,2.4C1,2.6,0.3,3.5,0.2,4.8C0,6.1,0,8,0,8 s0,1.9,0.2,3.2c0.2,1.3,0.8,2.2,2.2,2.4C4.6,14,8,14,8,14s3.4,0,5.6-0.4c1.4-0.3,2-1.1,2.2-2.4C16,9.9,16,8,16,8S16,6.1,15.8,4.8z M6,11V5l5,3L6,11z"
                                                ></path>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="ml-1 lg:ml-1.5">YouTube</span>
                                </a>
                            </li>

                            <li class="col-span-12">
                                <a class="footer-v6__link inline-flex items-center" :href="facebook">
                                    <span class="footer-v6__link-icon" aria-hidden="true">
                                        <svg
                                            class="icon inline-block h-[1em] w-[1em] shrink-0 fill-current leading-none text-inherit"
                                            viewBox="0 0 16 16"
                                        >
                                            <g>
                                                <path
                                                    d="M16,8.048a8,8,0,1,0-9.25,7.9V10.36H4.719V8.048H6.75V6.285A2.822,2.822,0,0,1,9.771,3.173a12.2,12.2,0,0,1,1.791.156V5.3H10.554a1.155,1.155,0,0,0-1.3,1.25v1.5h2.219l-.355,2.312H9.25v5.591A8,8,0,0,0,16,8.048Z"
                                                ></path>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="ml-1 lg:ml-1.5">Facebook</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                        <div class="mb-3 lg:mb-5">
                            <h4 class="mb-1 text-lg">Contacto</h4>
                            <ul class="grid grid-cols-12 gap-2 lg:gap-3 lg:text-base">
                                <li class="col-span-12">
                                    <a class="footer-v6__link inline-flex items-center" href="#0">
                                        <span class="footer-v6__link-icon" aria-hidden="true"> Whatsapp icon </span>
                                        <span class="ml-1 tracking-wider lg:ml-1.5">{{ company.phone }}</span>
                                    </a>
                                </li>

                                <li class="col-span-12">
                                    <a class="footer-v6__link inline-flex items-center" href="#0">
                                        <Mail />
                                        <span class="ml-1 tracking-wider lg:ml-1.5">{{ company.email }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-zinc-900/10 py-3 lg:py-5">
                <p class="text-xs text-zinc-500">&copy; {{ new Date().getFullYear() }} Dogstyles - Todos los derechos reservados</p>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { Company, Menu } from '@/types';
import { Mail } from 'lucide-vue-next';

import { Link } from '@inertiajs/vue3';

const { company } = defineProps<{
    company: Company;
    footerMenu: Menu;
    legalMenu: Menu;
}>();

const { socialMedia } = company;

const facebook = socialMedia.facebook || '';
const instagram = socialMedia.instagram || '';
const twitter = socialMedia.twitter || '';
const youtube = socialMedia.youtube || '';
</script>

<style scoped></style>
