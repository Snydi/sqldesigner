<template>
    <div class="feedback-overlay" @click.self="$emit('close')">
        <div class="feedback-modal">
            <div class="feedback-modal__header">
                <span class="feedback-modal__title">Feedback</span>
                <button class="feedback-modal__close" @click="$emit('close')">
                    <img src="../icons/close.svg" style="width:14px;height:14px;filter:brightness(0) invert(1);" />
                </button>
            </div>

            <div class="feedback-modal__body">
                <p class="feedback-modal__intro">
                    Hi, it's Dmitriy, creator of this website.
                    If you have any complaints or suggestions,
                    feel free to use this form, send me a message directly at
                    <a class="feedback-modal__email-link" @click.prevent="copyEmail">snydi611@gmail.com</a>,
                    or join the
                    <a class="feedback-modal__discord-link" href="https://discord.gg/vFwgX7qKqA" target="_blank" rel="noopener noreferrer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="currentColor" aria-hidden="true" style="vertical-align:-1px"><path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
                        Discord server
                    </a>.
                    <br>
                    I check my emails everyday and will soon fix any problem you might be facing.
                </p>

                <label class="feedback-modal__anon-row">
                    <input type="checkbox" v-model="anonymous" />
                    <span>Send anonymously</span>
                </label>

                <input
                    v-if="!anonymous"
                    class="feedback-modal__input"
                    type="email"
                    placeholder="Your email"
                    v-model="email"
                />

                <textarea
                    class="feedback-modal__textarea"
                    placeholder="Your message…"
                    v-model="message"
                    rows="5"
                ></textarea>

                <p v-if="error" class="feedback-modal__error">{{ error }}</p>
                <p v-if="success" class="feedback-modal__success">Message sent! Thank you.</p>
            </div>

            <div class="feedback-modal__footer">
                <button class="btn btn-secondary" @click="$emit('close')">Cancel</button>
                <button class="btn btn-primary" @click="submit" :disabled="loading">
                    {{ loading ? 'Sending…' : 'Send' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@/axios'
import { useToast } from 'vue-toast-notification'

const props = defineProps({ userEmail: { type: String, default: '' } })
const emit = defineEmits(['close'])
const $toast = useToast()

const anonymous = ref(false)
const email = ref(props.userEmail)
const message = ref('')
const loading = ref(false)
const error = ref('')
const success = ref(false)

const copyEmail = () => {
    navigator.clipboard.writeText('snydi611@gmail.com')
    $toast.success('Email copied to clipboard')
}

const submit = async () => {
    error.value = ''
    if (!message.value.trim()) { error.value = 'Please write a message.'; return }
    if (!anonymous.value && !email.value.trim()) { error.value = 'Please enter your email or send anonymously.'; return }

    loading.value = true
    try {
        await axios.post('/api/feedback', {
            message: message.value,
            email: anonymous.value ? null : email.value,
        })
        success.value = true
        setTimeout(() => emit('close'), 1500)
    } catch {
        error.value = 'Something went wrong. Please try again.'
    } finally {
        loading.value = false
    }
}
</script>
