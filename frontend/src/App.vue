<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isVisible" class="callback-modal-overlay" @click.self="closeModal">
        <div class="callback-modal">
          <button class="callback-modal__close" @click="closeModal">&times;</button>

          <h3 class="callback-modal__title">Обратная связь</h3>

          <form @submit.prevent="submitForm" class="callback-form">
            <div
                v-for="field in fields"
                :key="field.sid"
                class="callback-form__group"
            >
              <label :for="`field-${field.sid}`" class="callback-form__label">
                {{ field.caption }}
                <span v-if="field.required" class="required">*</span>
              </label>

              <input
                  v-if="field.type === 'text'"
                  :id="`field-${field.sid}`"
                  v-model="formData[field.sid]"
                  type="text"
                  class="callback-form__input"
                  :class="{ 'error': errors[field.sid] }"
                  :placeholder="getPlaceholder(field.sid)"
                  @blur="validateField(field)"
              />

              <textarea
                  v-else-if="field.type === 'textarea'"
                  :id="`field-${field.sid}`"
                  v-model="formData[field.sid]"
                  class="callback-form__textarea"
                  :class="{ 'error': errors[field.sid] }"
                  rows="3"
                  @blur="validateField(field)"
              ></textarea>

              <div v-else-if="field.type === 'checkbox'" class="callback-form__checkbox-wrapper">
                <input
                    :id="`field-${field.sid}`"
                    v-model="formData[field.sid]"
                    type="checkbox"
                    class="callback-form__checkbox"
                />
                <label :for="`field-${field.sid}`" class="callback-form__checkbox-label">
                  {{ field.caption }}
                </label>
              </div>

              <span v-if="errors[field.sid]" class="callback-form__error">
                {{ errors[field.sid] }}
              </span>
            </div>

            <button type="submit" class="callback-form__submit" :disabled="isSubmitting">
              {{ isSubmitting ? 'Отправка...' : 'Отправить' }}
            </button>

            <p v-if="submitMessage" class="callback-form__message" :class="submitStatus">
              {{ submitMessage }}
            </p>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import './components/CallbackModal.css';

const props = defineProps({
  formData: Object,
});

console.log(props)

const isVisible = ref(false);
const isSubmitting = ref(false);
const submitMessage = ref('');
const submitStatus = ref('');

const formId = props.formData.formId;
const sessid = props.formData.sessid;
const questions = props.formData.questions || [];

const fields = ref(questions);

const formData = reactive({});
const errors = reactive({});

onMounted(() => {
  fields.value.forEach(field => {
    formData[field.sid] = field.value || (field.type === 'checkbox' ? false : '');
  });

  setTimeout(() => {
    isVisible.value = true;
  }, 1000);
});

const validateField = (field) => {
  const value = formData[field.sid];

  if (field.required && (!value || value === '')) {
    errors[field.sid] = 'Поле обязательно для заполнения';
    return false;
  }

  if (field.sid === 'PHONE' && value) {
    const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,20}$/;
    if (!phoneRegex.test(value)) {
      errors[field.sid] = 'Введите корректный номер телефона';
      return false;
    }
  }

  errors[field.sid] = '';
  return true;
};

const validateAll = () => {
  let isValid = true;
  fields.value.forEach(field => {
    if (!validateField(field)) {
      isValid = false;
    }
  });
  return isValid;
};

const submitForm = async () => {
  if (!validateAll()) return;

  isSubmitting.value = true;
  submitMessage.value = '';

  try {
    const payload = new FormData();
    payload.append('FORM_ID', formId);
    payload.append('sessid', sessid);

    fields.value.forEach(field => {
      const value = formData[field.sid];

      if (field.type === 'checkbox') {
        payload.append(`data[form_checkbox_${field.id}]`, value ? 'Y' : 'N');
      } else if (field.type === 'textarea') {
        payload.append(`data[form_textarea_${field.id}]`, value ?? '');
      } else if (field.type === 'text') {
        payload.append(`data[form_text_${field.id}]`, value ?? '');
      } else {
        payload.append(`data[form_text_${field.id}]`, value ?? '');
      }
    });

    const response = await fetch(props.formData.submitUrl, {
      method: 'POST',
      body: payload,
      credentials: 'same-origin',
    });

    const result = await response.json();

    if (result.success) {
      submitStatus.value = 'success';
      submitMessage.value = 'Спасибо! Мы перезвоним вам в ближайшее время.';

      setTimeout(() => {
        closeModal();
      }, 2000);
    } else {
      submitStatus.value = 'error';
      submitMessage.value = result.error || 'Ошибка отправки';
    }
  } catch (e) {
    submitStatus.value = 'error';
    submitMessage.value = 'Ошибка соединения. Попробуйте позже.';
  } finally {
    isSubmitting.value = false;
  }
};

const getPlaceholder = (sid) => {
  const placeholders = {
    'NAME': 'Введите ваше имя',
    'PHONE': '+7 (___) ___-__-__',
    'COMMENT': 'Комментарий (необязательно)',
  };
  return placeholders[sid] || '';
};

const closeModal = () => {
  isVisible.value = false;
};
</script>