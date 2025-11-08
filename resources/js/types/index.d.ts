import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Plan {
  id: number;
  name: string;
  price: number;
  duration_days: number;
  product_limit?: number | null;
  order_limit?: number | null;
  customer_limit?: number | null;
  storage_limit_mb?: number | null;
  store_limit: number;
  custom_domain: boolean;
  advanced_analytics: boolean;
  staff_accounts: boolean;
  priority_support: boolean;
  features?: any[] | null; 
  created_at: string;
  updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Category {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  created_by: string;
  last_updated_by: string;
  parent_id: string | null;
  status: number;
  order_level: number;
  type: 'mockup' | 'ecommerce';
  print_type: 'default_print' | 'all_over_print';
  title: string;
  image: string | null;
  creator: User;
  editor: User;
  shipping_charge: {
    us_charge: number;
    us_add_charge_per_item: number;
    worldwide_charge: number;
    worldwide_add_charge_per_item: number;
  }
}

export interface AdditionalShippingCharge {
  id: string;
  name: string;
  description: string | null;
  us_charge: number;
  worldwide_charge: number;
  status: boolean;
  order_level: number;
  created_by: string | null;
  last_updated_by: string | null;
  created_at: string;
  updated_at: string;
  creator: User;
  editor: User;
}

export interface Badge {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  created_by: string;
  last_updated_by: string;
  status: number;
  order_level: number;
  creator: User;
  editor: User;
}

export interface Mockup {
  id: string;
  category_id: string;
  name: string;
  description: string;
  image: string;
  model: string;
  type: string;
  price: number;
  sell_price: number;
  order_level: number;
  status: number;
  created_by: string;
  last_updated_by: string;
  created_at: string;
  updated_at: string;
  type: string;
  video_url: string | null,
  shipping_charge: {
    us_charge: number;
    us_add_charge_per_item: number;
    worldwide_charge: number;
    worldwide_add_charge_per_item: number;
  };
  color_ids: string[];
  colors: string[];
  category: Category;
  creator: User;
  editor: User;
  size_chart: {
    chart_data: {
      columns: any[],
      rows: any[],
    }
  };
}

export interface MockupSide {
  id: string;
  name: string;
  description: string;
  image: string;
  model: string;
  type: string;
  mockup?: Mockup;
}

export interface Color {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  created_by: string;
  last_updated_by: string;
  status: boolean;
  order_level: number;
  hex_code: string;
  creator: User;
  editor: User;
}

export interface Size {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  created_by: string;
  last_updated_by: string;
  status: boolean;
  order_level: number;
  creator: User;
  editor: User;
}



export interface State {
  id: string;
  name: string;
  country: Country;
  created_at: string;
  updated_at: string;
  created_by: string;
  last_updated_by: string;
  status: boolean;
  code: string;
  country_id: string;
  shipping_charge: number;
  tax_percentage: number;
  vat_percentage: number;
  capital: string;
  timezone: string;
  iso_code: string;
  currency: string;
  is_default: boolean;
  order_level: number;
  creator: User;
  editor: User;
}

export interface Country {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  created_by: string; 
  last_updated_by: string;
  status: boolean;
  creator: User;
  editor: User;
  states: State[];
}

export interface City {
  id: string;
  name: string;
  state: State;
  created_at: string;
  updated_at: string;
  created_by: string;
  country_id: string;
  state_id: string;
  shipping_charge: number;
  tax_percentage: number;
  vat_percentage: number;
  order_level: number;
  status: boolean;
  creator: User;
  editor: User;
  country: Country;
  state: State;
}

export interface Campaign {
  id: string;
  name: string;
  slug: string;
  description: string;
  start_date: string;
  sale: number;
  view: number;
  status: string;
  store: Store;
}
export interface Product {
  id: string;
  name: string;
  image: string;
  unit_price: number;
  type: string;
  images: {
    id: string;
    image: string;
    hex_code: string;
    images?: string[]; 
  }[];
}

export interface MockupVariation {
  id: string;
  mockup_id: string;
  size_id: string;
  created_by: string | null;
  last_updated_by: string | null;
  sku: string;
  price: number | null;
  sell_price: number;
  status: boolean;
  created_at: string;
  updated_at: string;
  mockup?: Mockup; // Relationship
  size?: Size; // Relationship
}
