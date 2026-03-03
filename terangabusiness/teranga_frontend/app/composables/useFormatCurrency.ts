export const useFormatCurrency = () => {
  const formatMontantFCFA = (amount: number): string => {
    return new Intl.NumberFormat('fr-SN', {
      style: 'currency',
      currency: 'XOF',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount)
  }

  return { formatMontantFCFA }
}
