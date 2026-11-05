export type Sex = 'homme' | 'femme';
export type Experience = 'debutant' | 'intermediaire' | 'avance';
export type CoachTone = 'sergent' | 'cash' | 'mix';
export type Pref = 'like' | 'dislike';
export type DayKey = 'haut-a' | 'bas-a' | 'haut-b' | 'bas-b';

export interface Profile {
  name: string;
  age: number;
  sex: Sex;
  heightCm: number;
  startWeight: number;
  goalWeight: number;
  milestoneWeight: number;
  /** Jours de salle, valeurs JS getDay() : 0 = dimanche … 6 = samedi */
  gymDays: number[];
  /** Heure habituelle de séance "HH:MM" */
  sessionTime: string;
  sessionMinutes: number;
  experience: Experience;
  kneeSensitive: boolean;
  coachTone: CoachTone;
  /** YYYY-MM-DD */
  startDate: string;
}

export interface WeightEntry {
  date: string;
  kg: number;
}

export interface SetEntry {
  weight: number;
  reps: number;
  done: boolean;
}

export interface WorkoutExercise {
  exerciseId: string;
  slot: string;
  sets: SetEntry[];
  target: { weight: number; reps: number };
}

export interface CardioEntry {
  id: string;
  minutes: number;
  done: boolean;
}

export interface ActiveWorkout {
  id: string;
  dayKey: DayKey;
  lazy: boolean;
  startedAt: number;
  date: string;
  exercises: WorkoutExercise[];
  cardio: CardioEntry;
  /** index de l'exercice affiché ; exercises.length = étape cardio */
  current: number;
  kneeFlag: boolean;
}

export interface WorkoutLog {
  id: string;
  dayKey: DayKey;
  lazy: boolean;
  date: string;
  startedAt: number;
  durationMin: number;
  exercises: WorkoutExercise[];
  cardio: CardioEntry;
  xp: number;
  prs: string[];
  volume: number;
  kneeFlag: boolean;
}

export type Verdict = 'top' | 'ok' | 'bof';

export interface Meal {
  id: string;
  date: string;
  time: string;
  label: string;
  kcal: number;
  protein: number;
  carbs: number;
  fat: number;
  verdict: Verdict;
  comment: string;
  source: 'photo' | 'texte' | 'rapide';
  items?: { name: string; kcal: number }[];
  hasThumb?: boolean;
}

export interface Settings {
  apiKey: string;
  notifGym: boolean;
  notifMissed: boolean;
  notifWeigh: boolean;
  notifMeals: boolean;
  kcalOverride: number | null;
  restTimer: boolean;
}

export interface WorkoutSummary {
  log: WorkoutLog;
  rankUp: string | null;
  message: string;
}
