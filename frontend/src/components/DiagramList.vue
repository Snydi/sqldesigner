<template>
    <div class="centered-container">
        <div class="form-container">
            <a href="/" class="float-left">
                <img src="../icons/logo.svg" alt="sql-designer" class="logo">
            </a>
            <br>
            <h2 class="form-title">Diagrams</h2>

            <div class="flex-centered flex-items">
                <img @click="addDiagram" src="../icons/checkmark.svg" class="icon" alt="checkmark">
                <input
                    type="text"
                    v-model="newDiagramName"
                    placeholder="Enter diagram name"
                    class="input input-underline"
                />
                <img @click="this.newDiagramName = ''" src="../icons/cancel-colored.svg" class="icon"
                     alt="cancel-colored">
            </div>
            <br>
            <ul class="list">
                <li v-for="diagram in diagrams" :key="diagram.id">
                    <div class="flex-centered flex-items mt-10">
                        <img @click="viewDiagram(diagram.id)" src="../icons/eye.svg" class="icon" alt="eye">
                        <input
                            type="text"
                            v-model="diagram.name"
                            @focus="backupName(diagram)"
                            @change="updateDiagram(diagram)"
                            class="input input-underline"
                        />
                        <img @click="deleteDiagram(diagram.id)" src="../icons/cancel-colored.svg" class="icon"
                             alt="cancel-colored">
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import router from '../router/index.js'
import { useToast } from 'vue-toast-notification'

const $toast = useToast()

export default {

    data() {
        return {
            diagrams: [],
            newDiagramName: '',
            originalName: null
        }
    },
    methods: {
        viewDiagram(id) {
            router.push({ path: `/diagrams/${id}` })
        },
        async addDiagram() {
            const response = await axios.post('/api/diagrams', {
                name: this.newDiagramName
            })
            this.newDiagramName = ''
            await this.fetchDiagrams()
            response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)

        },
        async updateDiagram(diagram) {
            const response = await axios.put(`/api/diagrams/${diagram.id}`, { name: diagram.name })
            await this.fetchDiagrams()
            this.originalName = null
            response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        },
        async deleteDiagram(id) {
            const response = await axios.delete(`/api/diagrams/${id}`)
            await this.fetchDiagrams()
            response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        },
        backupName(diagram) {
            this.originalName = diagram.name
        },
        async fetchDiagrams() {
            try {
                const response = await axios.get(`/api/diagrams`)
                this.diagrams = response.data.data
            } catch (error) {
                if (error.response) {
                    $toast.error(error.response.data.message)
                } else {
                    $toast.error('Something went wrong!')
                }
            }
        }
    },
    created() {
        this.fetchDiagrams()
    }
}
</script>
