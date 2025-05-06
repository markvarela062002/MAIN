<template>
    <div class="card">
      <h2 class="section-title">Physical Maturity</h2>
      <div class="table-container">
        <table class="assessment-table">
          <thead>
            <tr>
              <th>Physical Maturity Sign</th>
              <th v-for="score in [-1, 0, 1, 2, 3, 4, 5]" :key="'p-h-' + score">{{ score }}</th>
              <th class="recorded-column">Score</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(sign, index) in physicalSigns" :key="'p-' + sign.name">
              <td class="sign-name"><strong>{{ sign.name }}</strong></td>
              <td
                v-for="(desc, scoreIndex) in sign.scores"
                :key="'p-' + index + '-' + scoreIndex"
                @click="setScore(index, scoreIndex - 1)"
                :class="['clickable', scores[index] === scoreIndex - 1 ? 'selected' : '', desc === '' ? 'disabled' : '']"
              >
                {{ desc }}
              </td>
              <td class="recorded-column">{{ scores[index] !== null ? scores[index] : '-' }}</td>
            </tr>
            <tr class="table-footer">
              <td colspan="8" class="table-total-label">Total Physical Score:</td>
              <td class="table-total-value">{{ total }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent, PropType } from 'vue';
  
  interface PhysicalSign {
    name: string;
    scores: string[];
  }
  
  export default defineComponent({
    name: 'PhysicalTable',
    props: {
      modelValue: {
        type: Array as PropType<(number | null)[]>,
        required: true
      },
      total: {
        type: Number,
        required: true
      }
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
      const physicalSigns: PhysicalSign[] = [
        {
          name: "Skin",
          scores: [
            "Sticky, friable, transparent",
            "Gelatinous, red, translucent",
            "Smooth, pink, visible veins",
            "Superficial peeling, rash, few veins",
            "Cracking, pale areas, rare veins",
            "Parchment, deep cracking, no vessels",
            "Leathery, cracked, wrinkled"
          ]
        },
        {
          name: "Lanugo",
          scores: [
            "None",
            "Sparse",
            "Abundant",
            "Thinning",
            "Bald areas",
            "Mostly bald",
            ""
          ]
        },
        {
          name: "Plantar Surface",
          scores: [
            "Heel-toe 40-50 mm: -1",
            ">50 mm, no crease",
            "Faint red marks",
            "Anterior transverse crease only",
            "Creases anterior 2/3",
            "Creases over entire sole",
            ""
          ]
        },
        {
          name: "Breast",
          scores: [
            "Imperceptible",
            "Barely perceptible",
            "Flat areola, no bud",
            "Stippled areola, 1-2 mm bud",
            "Raised areola, 3-4 mm bud",
            "Full areola, 5-10 mm bud",
            ""
          ]
        },
        {
          name: "Eye/Ear",
          scores: [
            "Lids fused, loosely",
            "Lids open, slow recoil",
            "Not-curved pinna, soft recoil",
            "Well-curved pinna, soft recoil",
            "Formed & firm, instant recoil",
            "Thick cartilage, ear stiff",
            ""
          ]
        },
        {
          name: "Genitals (Male)",
          scores: [
            "Scrotum flat, smooth",
            "Scrotum empty, faint rugae",
            "Testes in canal, rare rugae",
            "Testes descending, few rugae",
            "Testes down, good rugae",
            "Testes pendulous, deep rugae",
            ""
          ]
        },
        {
          name: "Genitals (Female)",
          scores: [
            "Clitoris prominent, labia flat",
            "Prominent clitoris, small labia",
            "Prominent clitoris, enlarging labia",
            "Majora & minora equal",
            "Majora large, minora small",
            "Majora covers minora",
            ""
          ]
        }
      ];
  
      const scores = props.modelValue;
  
      const setScore = (index: number, score: number) => {
        if (physicalSigns[index].scores[score + 1] !== '') {
          const newScores = [...scores];
          newScores[index] = score;
          emit('update:modelValue', newScores);
        }
      };
  
      return {
        physicalSigns,
        scores,
        setScore
      };
    }
  });
  </script>
  
  <style scoped>
  .card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
  }
  
  .section-title {
    margin-top: 0;
    color: #2c3e50;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
  }
  
  .table-container {
    overflow-x: auto;
  }
  
  .assessment-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }
  
  .assessment-table th {
    background-color: #f5f5f5;
    font-weight: 500;
    text-align: center;
  }
  
  .assessment-table td, .assessment-table th {
    padding: 8px 12px;
    border: 1px solid #ddd;
  }
  
  .sign-name {
    text-align: left;
    font-weight: 500;
  }
  
  .clickable {
    cursor: pointer;
  }
  
  .clickable:hover {
    background-color: #f0f0f0;
  }
  
  .selected {
    background-color: #e3f2fd;
  }
  
  .disabled {
    background-color: #fafafa;
    color: #ccc;
    cursor: not-allowed;
  }
  
  .recorded-column {
    font-weight: bold;
    text-align: center;
  }
  
  .table-footer {
    font-weight: bold;
  }
  
  .table-total-label {
    text-align: right;
  }
  
  .table-total-value {
    font-weight: bold;
    font-size: 1.1em;
    text-align: center;
  }
  </style>