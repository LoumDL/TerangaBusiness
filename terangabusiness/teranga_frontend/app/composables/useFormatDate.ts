export const useFormatDate = () => {
  const formatDate = (date: string): string => {
    return new Intl.DateTimeFormat('fr-SN', {
      day: '2-digit',
      month: 'long',
      year: 'numeric',
    }).format(new Date(date))
  }

  const formatPeriode = (periode: string): string => {
    const [year, month] = periode.split('-')
    const date = new Date(Number(year), Number(month) - 1, 1)
    return new Intl.DateTimeFormat('fr-SN', {
      month: 'long',
      year: 'numeric',
    }).format(date)
  }

  return { formatDate, formatPeriode }
}
