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
                    feel free to use this form or send me a message directly at
                    <a class="feedback-modal__email-link" @click.prevent="copyEmail">snydi611@gmail.com</a>.
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
