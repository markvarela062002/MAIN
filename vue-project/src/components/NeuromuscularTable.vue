<template>
    <div class="card">
      <h2 class="section-title">Neuromuscular Maturity</h2>
      
      <div class="table-container">
        <table class="assessment-table">
          <thead>
            <tr>
              <th>Sign</th>
              <th v-for="score in [-1, 0, 1, 2, 3, 4, 5]" :key="score">{{ score }}</th>
              <th>Score</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(sign, index) in neuromuscularSigns" :key="sign.name">
              <td class="sign-name">{{ sign.name }}</td>
              <td
                v-for="(desc, scoreIndex) in sign.scores"
                :key="`${index}-${scoreIndex}`"
                @click="setScore(index, scoreIndex - 1)"
                :class="{
                  'clickable': desc !== '',
                  'selected': getScore(index) === scoreIndex - 1,
                  'disabled': desc === ''
                }"
              >
                {{ desc }}
                <img v-if="sign.images && sign.images[scoreIndex]" 
                     :src="sign.images[scoreIndex]" 
                     :alt="desc"
                     class="score-image">
              </td>
              <td class="score-value">{{ getScore(index) ?? '-' }}</td>
            </tr>
            <tr class="table-footer">
              <td colspan="8">Total Neuromuscular Score:</td>
              <td class="total-value">{{ total }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent } from 'vue';
  
  interface NeuromuscularSign {
    name: string;
    scores: string[];
    images?: string[];
  }
  
  export default defineComponent({
    name: 'NeuromuscularTable',
    props: {
      scores: {
        type: Object as () => Record<string, number | undefined>,
        required: true
      },
      total: {
        type: Number,
        required: true
      }
    },
    emits: ['update:scores'],
    setup(props, { emit }) {
      const neuromuscularSigns: NeuromuscularSign[] = [
        {
          name: "Posture",
          scores: ["", "Slight Flexion", "Moderate Flexion", "Full Flexion", "Strong Flexion", "Full Recoil", ""],
          images: [
            "",
            require('@/assets/images/posture_0.png'),
            require('@/assets/images/posture_1.png'),
            require('@/assets/images/posture_2.png'),
            require('@/assets/images/posture_3.png'),
            require('@/assets/images/posture_4.png'),
            ""
          ]
        },
        // Add other signs similarly
      ];
  
      const scoreKeys = [
        'posture',
        'square_window',
        'arm_recoil',
        'popliteal_angle',
        'scarf_sign',
        'heel_to_ear'
      ];
  
      function getScore(index: number): number | undefined {
        const key = scoreKeys[index];
        return props.scores[key];
      }
  
      function setScore(index: number, value: number) {
        const key = scoreKeys[index];
        const newScores = { ...props.scores, [key]: value };
        emit('update:scores', newScores);
      }
  
      return {
        neuromuscularSigns,
        getScore,
        setScore
      };
    }
  });
  </script>
  
  <style scoped>
  /* Add your table styles here */
  </style>