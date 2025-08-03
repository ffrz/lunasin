import dayjs from 'dayjs';

export const formatNumber = (value, locale = 'id-ID', maxDecimals = 0) => {
  let number = value;

  if (number === null || number === undefined || isNaN(number)) {
    number = 0;
  }

  return new Intl.NumberFormat(locale, {
    minimumFractionDigits: maxDecimals,
    maximumFractionDigits: maxDecimals,
  }).format(number);
};

export function plusMinusSymbol(num) {
  return num > 0 ? '+' : '';
}

export function formatNumberWithSymbol(num) {
  return plusMinusSymbol(num) + formatNumber(num);
}

export function formateDatetime(val, fmt = 'DD/MM/YYYY HH:mm:ss', locale = 'id-ID') {
  let date;
  if (val instanceof Date) {
    date = val;
  }
  else if (typeof (val) === 'string') {
    date = new Date(val);
  }
  else {
    throw new Error('val must be string or Date object');
  }

  return dayjs(this.currentDate).format(fmt);
}

export function formateDate(val, fmt = 'DD/MM/YYYY', locale = 'id-ID') {
  let date;
  if (val instanceof Date) {
    date = val;
  }
  else if (typeof (val) === 'string') {
    date = new Date(val);
  }
  else {
    throw new Error('val must be string or Date object');
  }

  return dayjs(this.currentDate).format(fmt);
}

export function formateTime(val, fmt = 'HH:mm:ss', locale = 'id-ID') {
  let date;
  if (val instanceof Date) {
    date = val;
  }
  else if (typeof (val) === 'string') {
    date = new Date(val);
  }
  else {
    throw new Error('val must be string or Date object');
  }

  return dayjs(this.currentDate).format(fmt);
}
