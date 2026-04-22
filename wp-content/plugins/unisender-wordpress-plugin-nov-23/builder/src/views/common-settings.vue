<template>
  <div class="b-unisender-app-common-settings b-unisender-app__common-settings">
    <div class="b-unisender-app-common-settings__header">
      <div class="b-unisender-app-common-settings__tab-buttons">
        <div
          class="b-unisender-app-common-settings__tab-button"
          v-for="(item, index) in tabs.buttons"
          :key="`tab-button-${index}`"
          :class="index === activeTab ? 'active' : ''"
          @click="handleTabButtonClick(index)"
        >
          {{ item }}
        </div>
      </div>
      <div
        class="b-unisender-app-common-settings__toggle-button"
        :class="showContent ? 'active' : ''"
        @click="tabContentShowButtonHandle"
      >
        <span></span>
      </div>
    </div>
    <div
      class="b-unisender-app-common-settings__main"
      :class="showContent ? 'active' : ''"
    >
      <div
        class="b-unisender-app-common-settings__tab-content"
        :class="activeTab === 0 ? 'active' : ''"
      >
        <CommonSettingsForm></CommonSettingsForm>
      </div>
      <div
        class="b-unisender-app-common-settings__tab-content"
        :class="activeTab === 1 ? 'active' : ''"
      >
        <CommonSettingsMessages></CommonSettingsMessages>
      </div>
      <div
        class="b-unisender-app-common-settings__tab-content"
        :class="activeTab === 2 ? 'active' : ''"
      >
        <CommonSettingsSettingsSend></CommonSettingsSettingsSend>
      </div>
    </div>
  </div>
</template>

<script>
import CommonSettingsForm from "@/components/common-settings-form";
import CommonSettingsMessages from "@/components/common-settings-messages";
import CommonSettingsSettingsSend from "@/components/common-settings-settings-send";
export default {
  components: {
    CommonSettingsForm,
    CommonSettingsMessages,
    CommonSettingsSettingsSend,
  },
  data() {
    return {
      activeTab: 0,
      tabs: {
        buttons: ["Форма", "Сообщения", "Настройки отправки"],
      },
      showContent: true,
    };
  },
  methods: {
    handleTabButtonClick(index) {
      this.activeTab = index;
    },
    tabContentShowButtonHandle() {
      this.showContent = !this.showContent;
    },
  },
};
</script>