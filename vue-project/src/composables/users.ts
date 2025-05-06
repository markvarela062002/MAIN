import { ref, Ref } from "vue";
import axios from "axios";

interface BloodPressureChart {
    systolic: Array<[]>;
    diastolic: Array<[]>;
    labels: Array<[]>;
}

export function useUsers(page: string = "all") {
    const users: Ref<BloodPressureChart | null> = ref<BloodPressureChart | null>(null);

    const getUsers = async () => {
        try {
            const response = await axios.get(
                `/api/v1/users`,
                {
                    params: {
                        perPage: page,
                    },
                }
            );
            users.value = response.data;
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const getUser = async (id: string) => {
        try {
            const response = await axios.get(
                `/api/v1/users/${id}`,
                {
                    params: {
                        perPage: page,
                    },
                }
            );
            users.value = response.data.data;
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };



    return { users, getUsers, getUser };
}

