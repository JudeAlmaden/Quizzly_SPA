<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <div class="register-container">
        <!-- Animated Background -->
        <div class="background-animation">
            <div class="question-marks layer-1"></div>
            <div class="question-marks layer-3"></div>
        </div>

        <!-- Register Form -->
        <div class="register-form-wrapper">
            <div class="register-form">
                <h1 class="welcome-title">
                    Join <span class="gradient-text">Quizzly</span>
                </h1>

                <form @submit.prevent="submit">
                    <div class="input-group">
                        <input
                            id="name"
                            type="text"
                            class="form-input"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Your name"
                        />
                        <InputError class="error-message" :message="form.errors.name" />
                    </div>

                    <div class="input-group">
                        <input
                            id="email"
                            type="email"
                            class="form-input"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="error-message" :message="form.errors.email" />
                    </div>

                    <div class="input-group">
                        <input
                            id="password"
                            type="password"
                            class="form-input"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Password"
                        />
                        <InputError class="error-message" :message="form.errors.password" />
                    </div>

                    <div class="input-group">
                        <input
                            id="password_confirmation"
                            type="password"
                            class="form-input"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Confirm password"
                        />
                        <InputError
                            class="error-message"
                            :message="form.errors.password_confirmation"
                        />
                    </div>

                    <button
                        type="submit"
                        class="register-button"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Register
                    </button>
                </form>

                <p class="bottom-text">
                    <Link :href="route('login')" class="link-text">Already registered? Login here</Link>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.register-container {
    position: relative;
    min-height: 100vh;
    width: 100%;
    background: linear-gradient(135deg, #1a0b2e 0%, #2d1b4e 50%, #1a0b2e 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Animated Background */
.background-animation {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
}

.question-marks {
    position: absolute;
    width: 200%;
    height: 200%;
    background-image: url('/images/question-marks.png');
    background-repeat: repeat;
    background-size: 400px;
    opacity: 0.4;
}

.layer-1 {
    animation: scroll-diagonal-1 60s linear infinite;
}

.layer-2 {
    animation: scroll-diagonal-2 80s linear infinite;
    opacity: 0.3;
}

.layer-3 {
    animation: scroll-diagonal-3 100s linear infinite;
    opacity: 0.2;
}

@keyframes scroll-diagonal-1 {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(-50%, -50%);
    }
}

@keyframes scroll-diagonal-2 {
    0% {
        transform: translate(-25%, -25%) rotate(0deg);
    }
    100% {
        transform: translate(-75%, -75%) rotate(360deg);
    }
}

@keyframes scroll-diagonal-3 {
    0% {
        transform: translate(-10%, -10%);
    }
    100% {
        transform: translate(-60%, -60%);
    }
}

/* Register Form */
.register-form-wrapper {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 450px;
    padding: 20px;
}

.register-form {
    background: rgba(20, 10, 40, 0.7);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 48px 40px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.welcome-title {
    text-align: center;
    font-size: 32px;
    font-weight: 700;
    color: white;
    margin-bottom: 40px;
    line-height: 1.2;
}

.gradient-text {
    background: linear-gradient(135deg, #00f5ff 0%, #ff00ff 50%, #ffaa00 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 800;
}

.input-group {
    margin-bottom: 24px;
}

.form-input {
    width: 100%;
    padding: 16px 0;
    background: transparent;
    border: none;
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 16px;
    outline: none;
    transition: all 0.3s ease;
}

.form-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.form-input:focus {
    border-bottom-color: #00f5ff;
}

.error-message {
    margin-top: 8px;
    color: #ff4d6d;
    font-size: 14px;
}

.register-button {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #00d9ff 0%, #00b8d4 100%);
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 32px;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 10px 30px rgba(0, 217, 255, 0.3);
}

.register-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0, 217, 255, 0.5);
}

.register-button:active:not(:disabled) {
    transform: translateY(0);
}

.bottom-text {
    text-align: center;
    margin-top: 32px;
}

.link-text {
    color: #00f5ff;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.link-text:hover {
    color: #00ffcc;
}

/* Responsive */
@media (max-width: 640px) {
    .register-form {
        padding: 32px 24px;
    }

    .welcome-title {
        font-size: 28px;
    }

    .form-input {
        font-size: 14px;
        padding: 14px 0;
    }

    .register-button {
        font-size: 16px;
        padding: 14px;
    }
}
</style>
