<?php
const PAYMENT_TYPES = [
  'naqd' => 'Naqd pul UZS',
  'dollar' => 'Naqd pul USD',
  'kassa' => 'Naqd pul KASSA',
  'plastik' => 'Plastik Karta',
  'payme' => 'Payme',
  'bank_hisob' => 'Bank',
  'click' => 'Click',
  'uzum' => 'Uzum Bank',
  'apelsin' => 'Apelsin',
  'cloudPayments' => 'Cloud Payments',
  'uzumNasiya' => 'Uzum Nasiya',
  'itBilim' => 'IT Bilim',
  'stuff' => 'Hodim',
  'system' => 'Tizim',
  'terminal' => 'Terminal',
  'anor_bank' => 'Anor Bank',
  'zoodpay' => 'Zood Pay',
  'express' => 'Express',
  'vaucher' => 'Vaucher',
  'refund' => 'To\'lovni qaytarildi',
  'paynet' => 'Paynet',
];
const EXPANSE_TYPES = [
  'naqd' => 'Naqd pul UZS',
  'dollar' => 'Naqd pul USD',
  'kassa' => 'Kassa (Инкассация)',
  'taxi' => 'Taxi',
  'corp_card' => 'Karparativ Karta',
  'coin' => 'Coin',
  'vaucher' => 'Vaucher',
  'refund' => 'To\'lovni qaytarildi',
  'category' => [
    'qaytim' => 'Qaytim',
    'taksi' => 'Taksi',
    'tolov_qaytarildi' => 'To\'lov qaytarildi',
    'office_expense' => 'Office harajat',
    'inkas' => 'Inkassaciya',
    'head_office' => 'Head Office',
    'coin' => 'Coin',
    'refund' => 'To\'lovni qaytarildi',
  ]
];
const LOG_TYPES = [
  'add_student' => 'Talaba qo\'shish',
  'edit_student' => 'Talabani tahrirlash',
  'delete_student' => 'Talabani o\'chirish',
  'reminder_student' => 'Talabaga eslatma',
  'subtract_from_balance' => 'Balansdan ayirish',
  'change_price' => 'Narxni o\'zgartirish',
  'add_to_group' => 'Guruhga qo\'shish',
  'change_student_group' => 'Talabaning guruhini o\'zgartirish',
  'freeze_student' => 'Talabani muzlatish',
  'activate_student' => 'Talabani faollashtirish',
  'add_coins' => 'Coin qo\'shish',
  'add_payment' => 'To\'lov qo\'shish',
  'add_recalculation' => 'Qayta hisoblashni qo\'shish',
  'upload_student_file' => 'Talaba faylini yuklash',
  'delete_student_file' => 'Talaba faylini o\'chirish',
  'restore_student' => 'Talabani tiklash',
  'remove_coins' => 'Coin olib tashlash',
  'withdraw' => 'Oylik toʻlov yechib olindi',
  'open_student_task' => 'Vazifa qo\'shildi',
  'close_student_task' => 'Vazifa yopildi',
  'join_to_group' => 'Talabani guruhga qo\'shish',
  'edit_subscription' => 'Guruh obunani tahrirlash',
  'deactivate_old_subscription' => 'Eski obunani o\'chirish',
  'add_new_subscription' => 'Yangi obuna qo\'shing',
  'remove_from_group' => 'Guruhdan o\'chirish',
  'defrosted' => 'Muzlatishdan chiqgan',
];
const LEAD_STATUS = [
  'center_visit' => 'Markazga keldi',
  'demo_class_invite' => 'Sinov darsiga chaqirildi',
  'demo_class_visited' => 'Sinov darsiga keldi',
  'demo_class_not_visited' => 'Sinov darsiga kelmadi',
  'active' => 'Aktiv',
];

const DAYS_DATA = [
  '1,3,5' => 'Toq kunlar',
  '2,4,6' => 'Juft kunlar'
];

const HOURS_DATA = [
  '09:00',
  '09:30',
  '10:00',
  '10:30',
  '11:00',
  '11:30',
  '12:00',
  '12:30',
  '13:00',
  '13:30',
  '14:00',
  '14:30',
  '15:00',
  '15:30',
  '16:00',
  '16:30',
  '17:00',
  '17:30',
  '18:00',
  '18:30',
  '19:00',
  '19:30',
  '20:00'
];

const PHONE_TYPES = [
  'self' => 'Talaba',
  'mother' => 'Onasi',
  'father' => 'Otasi',
  'relative' => 'Qarindoshi'
];

const DISCOUNT_REASONS = [
  'tree_month_payment' => '3 oylik to’lov',
  'six_month_payment' => '6 oylik to’lov',
  'nine_month_payment' => '9 oylik to’lov',
  'two_students' => '2 ta o\'quvchi',
  'three_students' => '3 ta o\'quvchi',
];

const LEAD_STATUS_COLORS = [
  'center_visit' => 'badge-light-warning',
  'demo_class_invite' => 'badge-light-primary',
  'demo_class_visited' => 'badge-light-success',
  'demo_class_not_visited' => 'badge-light-danger',
  'active' => 'badge-light-info',
  'dark' => [
    'center_visit' => 'badge-warning',
    'demo_class_invite' => 'badge-primary',
    'demo_class_visited' => 'badge-success',
    'demo_class_not_visited' => 'badge-danger',
    'active' => 'badge-info',
  ]
];

define('DISCOUNT_OPTIONS', ['discount', 'package']);
define('DISCOUNT', 'discount');
define('PACKAGE', 'package');
