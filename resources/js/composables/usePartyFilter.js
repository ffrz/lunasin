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
      const needle = val.toLowerCase();
      const filteredList = parties.filter(
        (p) => p.label.toLowerCase().indexOf(needle) > -1
      );

      filteredList.unshift({
        label: `Tambah Pihak Baru "${val}"...`,
        value: "new_party",
        inputValue: val
      });

      filteredParties.value = filteredList;
    });
  };

  return {
    filteredParties,
    filterParties,
    parties,
  };
}
