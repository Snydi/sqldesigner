<template>
    <main class="billing-page">
        <div class="billing-shell">
            <div class="billing-heading">
                <div>
                    <p class="eyebrow">Account</p>
                    <h1>Billing and Pro access</h1>
                    <p>Pro is $10 USD per month (₽780) and renews automatically until you cancel.</p>
                </div>
                <span class="plan-badge" :class="{ 'plan-badge--pro': billing?.is_pro }">{{ billing?.is_pro ? 'Pro' : 'Free' }}</span>
            </div>

            <div v-if="loading" class="billing-card billing-state">Loading billing details…</div>
            <div v-else-if="loadError" class="billing-card billing-state billing-state--error">{{ loadError }}</div>

            <template v-else>
                <div v-if="paymentNotice" class="billing-notice" :class="`billing-notice--${paymentNotice.type}`">
                    {{ paymentNotice.message }}
                </div>

                <section class="billing-card current-plan">
                    <div>
                        <p class="card-label">Current access</p>
                        <h2>{{ billing.is_pro ? 'Pro — unlimited diagrams and exports' : 'Free plan' }}</h2>
                        <p v-if="billing.subscription?.provides_access">
                            Active until <strong>{{ formatDate(billing.subscription.ends_at) }}</strong>.
                            <span v-if="billing.subscription.status === 'cancelled'">Cancellation recorded; access continues until then.</span>
                        </p>
                        <p v-else>Upgrade for unlimited diagrams and exports with automatic monthly renewal.</p>
                    </div>
                    <div class="plan-actions">
                        <button v-if="billing.can_purchase" class="btn btn-primary" :disabled="checkoutLoading" @click="checkout">
                            {{ checkoutLoading ? 'Opening checkout…' : 'Start Pro — $10 USD/month (₽780)' }}
                        </button>
                        <button v-if="billing.can_cancel" class="btn btn-secondary" :disabled="cancelLoading" @click="cancel">
                            {{ cancelLoading ? 'Cancelling…' : 'Cancel Pro' }}
                        </button>
                    </div>
                </section>

                <section class="billing-card">
                    <div class="section-title"><h2>Redeem promo code</h2></div>
                    <form class="promocode-form" @submit.prevent="redeem">
                        <input v-model="promocode" maxlength="32" autocomplete="off" placeholder="Enter promo code" :disabled="promocodeLoading">
                        <button class="btn btn-secondary" type="submit" :disabled="promocodeLoading || !promocode.trim()">
                            {{ promocodeLoading ? 'Applying…' : 'Apply code' }}
                        </button>
                    </form>
                    <p class="promocode-help">Promo codes can be used once and add their Pro time to any active access.</p>
                </section>

                <section class="billing-card">
                    <div class="section-title"><h2>Payment history</h2><span>{{ billing.payments.length }} shown</span></div>
                    <div v-if="billing.payments.length" class="payment-list">
                        <div v-for="payment in billing.payments" :key="payment.id" class="payment-row">
                            <div>
                                <strong>{{ payment.amount }} {{ payment.currency }}</strong>
                                <span>{{ formatDate(payment.paid_at || payment.created_at) }}</span>
                            </div>
                            <span class="status" :class="`status--${payment.status}`">{{ payment.status }}</span>
                        </div>
                    </div>
                    <p v-else class="empty-state">No payments yet.</p>
                </section>

                <p class="billing-footnote">You may cancel at any time and request a refund under our <a href="/refund-policy">Refund Policy</a>.</p>
            </template>
        </div>
    </main>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toast-notification'
import { Subscription } from '@/services/Subscription.js'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const billing = ref(null)
const loading = ref(true)
const loadError = ref('')
const checkoutLoading = ref(false)
const cancelLoading = ref(false)
const promocodeLoading = ref(false)
const promocode = ref('')
const paymentNotice = ref(null)

const formatDate = (value) => value
    ? new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short', hourCycle: 'h23' }).format(new Date(value))
    : '—'

const load = async () => {
    try {
        billing.value = await Subscription.get()
        loadError.value = ''
    } catch (error) {
        loadError.value = error.response?.data?.message ?? 'Could not load billing details.'
    } finally {
        loading.value = false
    }
}

const checkout = async () => {
    checkoutLoading.value = true
    try {
        Subscription.submitCheckoutForm(await Subscription.checkout())
    } catch (error) {
        toast.error(error.response?.data?.message ?? 'Could not open checkout.')
        checkoutLoading.value = false
    }
}

const cancel = async () => {
    if (!window.confirm('Cancel Pro renewal? Your renewal will stop, and you may request a refund under our Refund Policy.')) return

    cancelLoading.value = true
    try {
        const result = await Subscription.cancel()
        toast.success(result.message)
        await load()
    } catch (error) {
        toast.error(error.response?.data?.message ?? 'Could not cancel Pro.')
    } finally {
        cancelLoading.value = false
    }
}

const redeem = async () => {
    promocodeLoading.value = true
    try {
        const result = await Subscription.redeem(promocode.value)
        toast.success(result.message)
        promocode.value = ''
        await load()
    } catch (error) {
        toast.error(error.response?.data?.message ?? 'Could not apply this promo code.')
    } finally {
        promocodeLoading.value = false
    }
}

onMounted(async () => {
    if (route.query.payment === 'processing') {
        paymentNotice.value = { type: 'success', message: 'Payment returned successfully. Pro activates when Robokassa confirms it; refresh shortly if it is still pending.' }
    } else if (route.query.payment === 'failed') {
        paymentNotice.value = { type: 'error', message: 'The payment was not completed. You can try again whenever you are ready.' }
    }
    if (route.query.payment) await router.replace({ name: 'billing' })
    await load()
})
</script>

<style scoped>
.billing-page { flex: 1; overflow-y: auto; background: var(--bg-elevated); padding: clamp(1.25rem, 4vw, 3rem) 1rem; text-align: left; }
.billing-shell { width: min(820px, 100%); margin: 0 auto; }
.billing-heading { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem; }
.eyebrow, .card-label { margin: 0 0 .45rem; color: var(--color-primary-text); font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
h1 { margin: 0; font-size: clamp(1.55rem, 4vw, 2.2rem); color: var(--text-primary); }
.billing-heading p:last-child { margin: .65rem 0 0; color: var(--text-secondary); line-height: 1.55; }
.plan-badge { flex-shrink: 0; padding: .35rem .75rem; border: 1px solid var(--border-color); border-radius: 999px; color: var(--text-secondary); font-size: .78rem; font-weight: 700; }
.plan-badge--pro { color: var(--color-primary-text); border-color: var(--color-primary); background: rgba(93,181,131,.08); }
.billing-card { margin-bottom: 1rem; padding: 1.4rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 10px; }
.billing-state { color: var(--text-secondary); text-align: center; }
.billing-state--error { color: #e69a9a; }
.billing-notice { margin-bottom: 1rem; padding: .9rem 1rem; border-radius: 8px; line-height: 1.5; }
.billing-notice--success { color: #b6e2c8; background: rgba(61,122,92,.22); border: 1px solid var(--color-primary); }
.billing-notice--error { color: #edb0b0; background: rgba(122,53,53,.2); border: 1px solid #7a3535; }
.current-plan { display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; }
.current-plan h2, .section-title h2 { margin: 0; color: var(--text-primary); font-size: 1.05rem; }
.current-plan p:not(.card-label) { margin: .55rem 0 0; color: var(--text-secondary); line-height: 1.55; }
.plan-actions { display: flex; flex-direction: column; align-items: stretch; gap: .55rem; flex-shrink: 0; }
.section-title { display: flex; justify-content: space-between; align-items: center; padding-bottom: .9rem; border-bottom: 1px solid var(--border-light); }
.section-title span { color: var(--text-muted); font-size: .75rem; }
.promocode-form { display:flex; gap:.65rem; margin-top:1rem; }
.promocode-form input { min-width:0; flex:1; padding:.7rem .8rem; border:1px solid var(--border-color); border-radius:6px; color:var(--text-primary); background:var(--bg-surface-alt); font:inherit; text-transform:uppercase; }
.promocode-help { margin:.65rem 0 0; color:var(--text-muted); font-size:.78rem; }
.payment-row { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: .9rem 0; border-bottom: 1px solid var(--border-light); }
.payment-row:last-child { border-bottom: 0; padding-bottom: 0; }
.payment-row strong { display: block; color: var(--text-primary); }
.payment-row div span { display: block; margin-top: .3rem; color: var(--text-muted); font-size: .78rem; }
.status { padding: .25rem .55rem; border-radius: 999px; color: var(--text-secondary); background: var(--bg-surface-alt); font-size: .7rem; text-transform: capitalize; }
.status--succeeded { color: #b6e2c8; background: rgba(61,122,92,.3); }
.status--failed { color: #edb0b0; background: rgba(122,53,53,.3); }
.empty-state, .billing-footnote { color: var(--text-muted); }
.billing-footnote { text-align: center; font-size: .78rem; }
@media (max-width: 620px) { .current-plan { align-items: stretch; flex-direction: column; } .plan-actions { width: 100%; } .promocode-form { flex-direction:column; } }
</style>
