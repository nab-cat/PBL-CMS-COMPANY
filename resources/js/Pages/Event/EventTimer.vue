<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

onMounted(() => {
    // Only access theme if it exists in page props
    if (page.props.theme && page.props.theme.secondary) {
        document.documentElement.style.setProperty(
            "--color-secondary",
            page.props.theme.secondary
        );
    }
});

const props = defineProps({
    targetDate: {
        type: [Date, String],
        required: true
    },
    finishedText: {
        type: String,
        default: 'Event telah selesai'
    }
});

const timeRemaining = ref({
    total: 0,
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0
});

const flipClass = ref({
    days: false,
    hours: false,
    minutes: false,
    seconds: false
});

let countdownInterval;

function updateCountdown() {
    const now = new Date().getTime();
    const target = new Date(props.targetDate).getTime();
    const distance = target - now;

    if (distance <= 0) {
        clearInterval(countdownInterval);
        timeRemaining.value = {
            total: 0,
            days: 0,
            hours: 0,
            minutes: 0,
            seconds: 0
        };
        return;
    }

    const prevSeconds = timeRemaining.value.seconds;
    const prevMinutes = timeRemaining.value.minutes;
    const prevHours = timeRemaining.value.hours;
    const prevDays = timeRemaining.value.days;

    const newTimeRemaining = {
        total: distance,
        days: Math.floor(distance / (1000 * 60 * 60 * 24)),
        hours: Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
        minutes: Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
        seconds: Math.floor((distance % (1000 * 60)) / 1000)
    };

    timeRemaining.value = newTimeRemaining;

    // Trigger flip animation when values change
    if (prevSeconds !== newTimeRemaining.seconds) {
        flipClass.value.seconds = true;
        setTimeout(() => { flipClass.value.seconds = false; }, 600);
    }
    
    if (prevMinutes !== newTimeRemaining.minutes) {
        flipClass.value.minutes = true;
        setTimeout(() => { flipClass.value.minutes = false; }, 600);
    }
    
    if (prevHours !== newTimeRemaining.hours) {
        flipClass.value.hours = true;
        setTimeout(() => { flipClass.value.hours = false; }, 600);
    }
    
    if (prevDays !== newTimeRemaining.days) {
        flipClass.value.days = true;
        setTimeout(() => { flipClass.value.days = false; }, 600);
    }
}

function startCountdown() {
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
}

watch(() => props.targetDate, () => {
    clearInterval(countdownInterval);
    startCountdown();
});

onMounted(() => {
    startCountdown();
});

onUnmounted(() => {
    clearInterval(countdownInterval);
});

function padZero(num) {
    return num.toString().padStart(2, '0');
}
</script>

<template>
    <div class="bg-secondary text-white rounded-xl p-6 sm:p-8 shadow w-full max-w-4xl mx-auto timer-container">
        <div
            v-if="timeRemaining.total > 0"
            class="flex flex-row justify-between items-center text-white text-center gap-2 sm:gap-6 md:gap-8"
        >
            <div class="countdown-item flex-1">
                <div 
                    :class="['time-number', {'flip-animation': flipClass.days}]" 
                    class="font-bold text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-tight"
                >
                    {{ padZero(timeRemaining.days) }}
                </div>
                <div class="text-xs sm:text-sm md:text-base mt-1 font-medium uppercase tracking-wide">
                    Hari
                </div>
            </div>

            <div class="countdown-divider text-2xl md:text-3xl font-light text-white/70">:</div>

            <div class="countdown-item flex-1">
                <div 
                    :class="['time-number', {'flip-animation': flipClass.hours}]" 
                    class="font-bold text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-tight"
                >
                    {{ padZero(timeRemaining.hours) }}
                </div>
                <div class="text-xs sm:text-sm md:text-base mt-1 font-medium uppercase tracking-wide">
                    Jam
                </div>
            </div>

            <div class="countdown-divider text-2xl md:text-3xl font-light text-white/70">:</div>

            <div class="countdown-item flex-1">
                <div 
                    :class="['time-number', {'flip-animation': flipClass.minutes}]" 
                    class="font-bold text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-tight"
                >
                    {{ padZero(timeRemaining.minutes) }}
                </div>
                <div class="text-xs sm:text-sm md:text-base mt-1 font-medium uppercase tracking-wide">
                    Menit
                </div>
            </div>

            <div class="countdown-divider text-2xl md:text-3xl font-light text-white/70">:</div>

            <div class="countdown-item flex-1">
                <div 
                    :class="['time-number', {'flip-animation': flipClass.seconds}]" 
                    class="font-bold text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-tight"
                >
                    {{ padZero(timeRemaining.seconds) }}
                </div>
                <div class="text-xs sm:text-sm md:text-base mt-1 font-medium uppercase tracking-wide">
                    Detik
                </div>
            </div>
        </div>
        <div
            v-else
            class="text-center text-white font-semibold text-xl"
        >
            {{ finishedText }}
        </div>
    </div>
</template>

<style scoped>
.timer-container {
    /* Replace gradient with solid background color */
    background-color: var(--color-secondary, #003366);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.countdown-item {
    position: relative;
    perspective: 300px;
}

.time-number {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 0.5rem;
    padding: 0.5rem;
    backface-visibility: hidden;
    transition: transform 0.6s;
    display: inline-block;
    min-width: 2ch;
}

.flip-animation {
    animation: flip-number 0.6s ease-in-out;
}

@keyframes flip-number {
    0% {
        transform: rotateX(0);
    }
    50% {
        transform: rotateX(90deg);
    }
    100% {
        transform: rotateX(0);
    }
}

.countdown-divider {
    margin-top: -0.5rem;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

@media (min-width: 768px) {
    .time-number {
        padding: 0.75rem;
    }
}
</style>