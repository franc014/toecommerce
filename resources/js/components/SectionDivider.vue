<template>
    <div class="section-divider">
        <svg viewBox="0 0 1920 60" aria-hidden="true">
            <path
                :class="fillColor"
                d="M1897,44c-27.225-3.493-36.3-15.483-67-20-36.435-5.361-56.969,8.474-89,16-61.3,14.4-80.376-1.993-139-14-83-17-181,34-638,34-1.329,0-2.665,0-4-.006S957.329,60,956,60C499,60,401,9,318,26c-58.624,12.007-77.7,28.4-139,14-32.031-7.526-52.565-21.361-89-16C59.3,28.517,50.225,40.507,23,44c-18.082,2.32-45.013-.8-81-28V83H1978V16C1942.013,43.2,1915.082,46.32,1897,44Z"
            />
        </svg>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const { backgroundColor } = defineProps<{ backgroundColor?: string }>();

const fillColor = computed(() => {
    return `${backgroundColor}`;
});
</script>

<style scoped>
/* --------------------------------

File#: _1_section-divider
Title: Section divider
Descr: A collection of SVG section dividers.
Usage: codyhouse.co/license

-------------------------------- */
:root {
    --section-divider-width: 1920;
    --section-divider-height: 60;
    --section-divider-ratio: calc(100% * var(--section-divider-height) / var(--section-divider-width));
}

[class*='has-section-divider'] {
    position: relative;
}

.has-section-dividers {
    padding-top: var(--section-divider-ratio);
    padding-bottom: var(--section-divider-ratio);
}
.has-section-dividers .section-divider + .section-divider {
    bottom: auto;
    top: -1px;
    -webkit-transform: scaleY(-1);
    transform: scaleY(-1);
}

.has-section-divider-top {
    padding-top: var(--section-divider-ratio);
}
.has-section-divider-top .section-divider {
    bottom: auto;
    top: -1px;
    -webkit-transform: scaleY(-1);
    transform: scaleY(-1);
}

.has-section-divider-bottom {
    padding-bottom: var(--section-divider-ratio);
}

.section-divider {
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    overflow: hidden;
}
.section-divider svg {
    position: relative;
    display: block;
    height: auto;
    max-width: none;
    width: 102%;
    left: -1%;
}
</style>
