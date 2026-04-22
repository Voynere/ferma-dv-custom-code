<template>
  <v-row>
    <v-col cols="12">
      <v-row :align="showTitleStyle ? 'start' : 'end'">
        <v-col cols="12" md="6">
          <Field
            :title="'Заголовок формы'"
            :value="title.value"
            @updateValue="updateTitle"
          ></Field>
        </v-col>
        <v-col cols="12" md="6">
          <v-row>
            <v-col cols="12">
              <span class="b-link" @click="showTitleStyle = !showTitleStyle">
                Настройка оформления
              </span>
            </v-col>
            <v-col cols="12" v-if="showTitleStyle" :class="'unisenderFadeIn'">
              <CommonSettingsStyles :type="'title'"></CommonSettingsStyles>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-col>
    <v-col cols="12">
      <v-row :align="showDescriptionStyle ? 'start' : 'end'">
        <v-col cols="12" md="6">
          <Field
            :title="'Описание формы'"
            :value="description.value"
            :type="'textarea'"
            @updateValue="updateDescription"
          ></Field>
        </v-col>
        <v-col cols="12" md="6">
          <v-row>
            <v-col cols="12">
              <span
                class="b-link"
                @click="showDescriptionStyle = !showDescriptionStyle"
              >
                Настройка оформления
              </span>
            </v-col>
            <v-col
              cols="12"
              v-if="showDescriptionStyle"
              :class="'unisenderFadeIn'"
            >
              <CommonSettingsStyles
                :type="'description'"
              ></CommonSettingsStyles>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-col>
    <v-col cols="12">
      <div class="b-label-title">Оформление формы</div>
      <v-row>
        <v-col cols="12">
          <v-row>
            <v-col cols="6">
              <span class="b-link" @click="showFormStyle = !showFormStyle">
                Настройка оформления
              </span>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="12" v-if="showFormStyle" :class="'unisenderFadeIn'">
          <v-row>
            <v-col cols="6">
              <CommonSettingsStyles :type="'form'"></CommonSettingsStyles>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-col>
    <v-col cols="12">
      <v-row :align="showButtonStyle ? 'start' : 'end'">
        <v-col cols="12" md="6">
          <Field
            :title="'Текст кнопки'"
            :value="button.value"
            @updateValue="updateButton"
          ></Field>
        </v-col>
        <v-col cols="12" md="6">
          <v-row>
            <v-col cols="12">
              <span class="b-link" @click="showButtonStyle = !showButtonStyle">
                Настройка оформления
              </span>
            </v-col>
            <v-col cols="12" v-if="showButtonStyle" :class="'unisenderFadeIn'">
              <CommonSettingsStyles :type="'button'"></CommonSettingsStyles>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-col>

    <!-- Тексареа для чекбокса политики конфиденциальности -->
    <v-col cols="12">
      <v-row :align="showPolicyStyle ? 'start' : 'end'">
        <v-col cols="12" md="6">
          <Field
            :title="'Политика конфиденциальности'"
            :value="policyConf.value"
            :type="'textarea'"
            @updateValue="updatePolicy"
          ></Field>
            <p style="padding-top: 5px;">
              Для того чтобы вставить ссылку, необходимо следовать правилам. 
              В круглых скобках (текст для ссылки)[https://www.example.com] 
              ссылка в квадратных скобках.
              Для обычного текста скобки использовать не нужно.
              Скопируйте это и отредактируйте только текст и ссылку
              <b> (text)[link] </b> 
            </p>
        </v-col>
        <v-col cols="12" md="6">
          <v-row>
            <v-col cols="12">
              <span class="b-link" @click="showPolicyStyle = !showPolicyStyle">
                Настройка оформления
              </span>
            </v-col>
            <v-col cols="12" v-if="showPolicyStyle" :class="'unisenderFadeIn'">
              <CommonSettingsStyles :type="'policyConf'"></CommonSettingsStyles>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-col>
  </v-row>
</template>

<script>
import Field from "@/components/field";
import CommonSettingsStyles from "@/components/common-settings-styles";
export default {
  components: {
    Field,
    CommonSettingsStyles,
  },
  data() {
    return {
      showTitleStyle: false,
      showDescriptionStyle: false,
      showFormStyle: false,
      showButtonStyle: false,
      showPolicyStyle: false,
    };
  },
  methods: {
    updateTitle(value) {
      this.$store.dispatch("editCommonSettingsTitleValue", value);
    },
    updateDescription(value) {
      this.$store.dispatch("editCommonSettingsDescriptionValue", value);
    },
    updateButton(value) {
      this.$store.dispatch("editCommonSettingsButtonValue", value);
    },
    updatePolicy(value) {
      this.$store.dispatch("editCommonSettingsPolicyValue", value);
    },
  },
  computed: {
    title() {
      return this.$store.getters.commonSettingsFormTitle;
    },
    description() {
      return this.$store.getters.commonSettingsFormDescription;
    },
    button() {
      return this.$store.getters.commonSettingsFormButton;
    },
    policyConf() {
      console.log(this.$store.getters.commonSettingsFormPolicy);
      return (
        this.$store.getters.commonSettingsFormPolicy || {
          value: null,
          styles: {},
        }
      );
    },
  },
};
</script>