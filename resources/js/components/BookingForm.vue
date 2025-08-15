<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="w-full max-w-md rounded-xl border border-black/40 bg-[#071717] p-8 shadow-2xl">
            <h2 class="mb-6 text-xl font-bold text-[#00f5a0]">New Booking</h2>

            <!-- Selected Date -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-1">Selected Date</label>
                <input 
                    type="date" 
                    v-model="form.selectedDate"
                    class="w-full rounded-lg border border-gray-600 bg-[#0d1f1f] p-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                />
            </div>

            <!-- Booking Type -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-1">Booking Type</label>
                <select 
                    v-model="form.bookingType" 
                    class="w-full rounded-lg border border-gray-600 bg-[#0d1f1f] p-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                >
                    <option value="">-- choose type --</option>
                    <option value="Sound System">Sound System</option>
                    <option value="Lighting">Lighting</option>
                    <option value="Stage Setup">Stage Setup</option>
                </select>
            </div>

            <!-- Materials -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Materials (check)</label>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-2 text-white">
                        <input type="checkbox" value="Microphone" v-model="form.materials" class="accent-[#00f5a0]">
                        Microphone
                    </label>
                    <label class="flex items-center gap-2 text-white">
                        <input type="checkbox" value="Speakers" v-model="form.materials" class="accent-[#00f5a0]">
                        Speakers
                    </label>
                    <label class="flex items-center gap-2 text-white">
                        <input type="checkbox" value="Cables" v-model="form.materials" class="accent-[#00f5a0]">
                        Cables
                    </label>
                    <label class="flex items-center gap-2 text-white">
                        <input type="checkbox" value="Lights" v-model="form.materials" class="accent-[#00f5a0]">
                        Lights
                    </label>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-1">Notes</label>
                <textarea 
                    v-model="form.notes" 
                    rows="3"
                    class="w-full rounded-lg border border-gray-600 bg-[#0d1f1f] p-2 text-white focus:border-[#00f5a0] focus:ring-1 focus:ring-[#00f5a0]"
                ></textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button
                    class="flex-1 rounded-lg border border-gray-500 px-4 py-2 font-bold text-white hover:bg-gray-700"
                    @click="$emit('close')"
                >
                    Cancel
                </button>
                <button
                    class="flex-1 rounded-lg bg-gradient-to-r from-[#00f5a0] to-[#00b2ff] px-4 py-2 font-bold text-black"
                    @click="submitBooking"
                >
                    Submit Booking
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                selectedDate: '',
                bookingType: '',
                materials: [],
                notes: ''
            }
        }
    },
    methods: {
        submitBooking() {
            console.log(this.form)
            // Booking submission logic
        }
    }
}
</script>
