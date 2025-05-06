<script lang="ts">
import { defineComponent, PropType } from 'vue';
import type { Patient } from '@/types/assessment';

export default defineComponent({
  props: {
    patientInfo: {
      type: Object as PropType<Patient>,
      required: true
    }
  },
  emits: ['update:patientInfo'],
  // ... rest of your component
});
</script>