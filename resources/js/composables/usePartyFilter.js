import { ref } from 'vue';

export function usePartyFilter(partiesRaw, includeAllOption = false) {
  const baseParties = partiesRaw.map((item) => {
    return { value: item.id, label: item.name };
  });

  const parties = includeAllOption
    ? [{ value: 'all', label: 'Semua' }, ...baseParties]
    : baseParties;

  const filteredParties = ref([...parties]);

  const filterParties = (val, update) => {
    update(() => {
      filteredParties.value = parties.filter(item =>
        item.label.toLowerCase().includes(val.toLowerCase())
      );
    });
  };

  return {
    filteredParties,
    filterParties,
    parties,
  };
}
