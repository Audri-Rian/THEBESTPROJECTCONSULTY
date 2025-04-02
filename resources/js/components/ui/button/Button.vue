<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Primitive, type PrimitiveProps } from 'radix-vue';
import type { HTMLAttributes } from 'vue';
import { buttonVariants, type ButtonVariants } from '.';
import { computed } from 'vue';

import * as LucideIcons from 'lucide-vue-next';

interface Props extends PrimitiveProps {
    variant?: ButtonVariants['variant'];
    size?: ButtonVariants['size'];
    class?: HTMLAttributes['class'];
    icon?: keyof typeof LucideIcons;
}

const props = withDefaults(defineProps<Props>(), {
    as: 'button',
});

const IconComponent = computed(() => (props.icon && LucideIcons[props.icon]) ? LucideIcons[props.icon] : null);
</script>

<template>
    <Primitive
        :as="as"
        :as-child="asChild"
        :class="cn(buttonVariants({ variant, size }), props.class)"
    >
        <component
            v-if="IconComponent"
            :is="IconComponent"
            class="w-4 h-4 shrink-0"
        />

        <slot />
    </Primitive>
</template>
