export interface User {
  id: number
  name: string
  email: string
}

export interface AuthResponse {
  token: string
  user: User
  message: string
}

export type StatutEnum = 'EN_ATTENTE' | 'VALIDÉ' | 'REJETÉ'

export interface Cotisation {
  id: number
  periode: string
  montant: number
  statut: StatutEnum
  created_at: string
}

export interface Paiement {
  id: number
  description: string
  montant: number
  statut: StatutEnum
  justificatif_url?: string
  created_at: string
}

export interface PaiementResponse {
  paiement: Paiement
  message: string
  ref: string
}

export type TypeTransaction = 'COTISATION' | 'PAIEMENT'

export interface Transaction {
  id: number
  type: TypeTransaction
  description: string
  montant: number
  statut: StatutEnum
  date: string
}

export interface DashboardData {
  solde_global: number
  cotisations: Cotisation[]
  cotisations_count: number
}

export interface HistoriqueData {
  transactions: Transaction[]
  total: number
}

export type FiltreType = 'jour' | 'mois' | 'annee' | null
