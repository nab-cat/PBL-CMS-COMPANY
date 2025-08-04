<template>
    <div class="relative font-custom antialiased">
        <main class="relative flex flex-col justify-center bg-secondary overflow-hidden">
            <div class="w-full max-w-5xl mx-auto px-4 md:px-6 py-24 text-center">

                <h2 class="text-2xl md:text-3xl font-bold text-white mb-10">
                    Ingin bermitra bersama kami?
                </h2>

                <div class="w-full inline-flex flex-nowrap overflow-hidden"
                    style="mask-image: linear-gradient(to right, transparent 0%, black 50%, transparent 100%)">
                    <ul ref="logoList"
                        class="flex items-center justify-center md:justify-start space-x-16 animate-infinite-scroll">
                        <li><img src="image/facebook.svg" alt="Facebook" class="h-12 max-w-none" /></li>
                        <li><img src="image/disney.svg" alt="Disney" class="h-12 max-w-none" /></li>
                        <li><img src="image/airbnb.svg" alt="Airbnb" class="h-12 max-w-none" /></li>
                        <li><img src="image/apple.svg" alt="Apple" class="h-12 max-w-none" /></li>
                        <li><img src="image/spark.svg" alt="Spark" class="h-12 max-w-none" /></li>
                        <li><img src="image/samsung.svg" alt="Samsung" class="h-12 max-w-none" /></li>
                        <li><img src="image/quora.svg" alt="Quora" class="h-12 max-w-none" /></li>
                        <li><img src="image/sass.svg" alt="Sass" class="h-12 max-w-none pr-10" /></li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const logoList = ref(null)

onMounted(() => {
    const ul = logoList.value
    if (ul) {
        const clone = ul.cloneNode(true)
        clone.setAttribute('aria-hidden', 'true')
        ul.parentNode.appendChild(clone)
    }
})
</script>

<style scoped>
@keyframes infinite-scroll {
    from {
        transform: translateX(0);
    }

    to {
        transform: translateX(-100%);
    }
}

.animate-infinite-scroll {
    animation: infinite-scroll 25s linear infinite;
}

.w-full {
    mask-image: linear-gradient(to right, transparent 0%, black 50%, transparent 100%);
}

</style>
