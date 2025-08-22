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
      filteredCategories.value = categories.filter(item =>
        item.label.toLowerCase().includes(val.toLowerCase())
      );
    });
  };

  const addCategory = (newCategory) => {
    const newItem = { value: newCategory.id, label: newCategory.name };
    categories.push(newItem);
    filteredCategories.value = [...categories];
    return newItem.value;
  }

  return {
    filteredCategories,
    filterCategories,
    categories, // jika butuh juga yang belum difilter
    addCategory
  };
}
