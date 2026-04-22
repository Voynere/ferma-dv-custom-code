<template>
    <v-row>
        <v-col cols="12" md="6">
            <v-row>
                <v-col cols="12"
                       v-for="item in doubleOptin"
                       :key="`double-optin-${item.value}`"
                >
                    <Radio
                        :label="item.label"
                        :value="item.value === commonSettingsSettingsSendDoubleOptin"
                        :name="'doubleOption'"
                        @updateValue="changeDoubleOption(item.value)"
                    ></Radio>
                </v-col>
            </v-row>
        </v-col>
        <v-col cols="12" md="6"></v-col>
    </v-row>
</template>

<script>
import Radio from '@/components/radio'
export default {
    components: {
        Radio
    },
    data() {
        return {}
    },
    methods: {
        changeDoubleOption(value) {
            this.$store.dispatch('setCommonSettingsSettingsSendDoubleOptin', value)
        }
    },
    computed: {
        doubleOptin() {
            return this.$store.getters.doubleOptin;
        },
        commonSettingsSettingsSendDoubleOptin() {
            return this.$store.getters.commonSettingsSettingsSend.doubleOptin
        }
    }
}
</script>