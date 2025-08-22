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

  const addParty = (newParty) => {
    const newItem = { value: newParty.id, label: newParty.name };
    parties.push(newItem);
    filteredParties.value = [...parties];
    return newItem.value;
  }

  return {
    filteredParties,
    filterParties,
    parties,
    addParty
  };
}
