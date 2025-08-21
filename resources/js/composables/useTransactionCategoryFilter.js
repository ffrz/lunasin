import { ref } from 'vue';

export function useTransactionCategoryFilter(rawCategories, includeAllOption = false) {
  const baseCategories = rawCategories.map(item => ({
    value: item.id,
    label: item.name
  }));

  const categories = includeAllOption
    ? [{ value: 'all', label: 'Semua' }, ...baseCategories]
    : baseCategories;

  const filteredCategories = ref([...categories]);

  const filterCategories = (val, update) => {
    update(() => {
      const needle = val.toLowerCase();
      const filteredList = categories.filter(
        (p) => p.label.toLowerCase().indexOf(needle) > -1
      );

      filteredList.unshift({
        label: `Tambah Kategori Baru "${val}"...`,
        value: "new_category",
        inputValue: val
      });

      filteredCategories.value = filteredList;
    });
  };

  return {
    filteredCategories,
    filterCategories,
    categories // jika butuh juga yang belum difilter
  };
}
