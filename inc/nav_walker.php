<?php
class Walker_Nav_Menu_Tailwind extends \Walker_Nav_Menu
{
    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        $indent = str_repeat("\t", $depth);

        // Clases adaptadas a tu estilo actual - hidden by default, shown via JS
        $classes = 'submenu pl-4 mt-2 space-y-2 font-bold italic text-sm hidden';

        $output .= "\n$indent<ul class=\"$classes\">\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;

        // Verificar si tiene submenús
        $has_children = !empty($args->walker->has_children);

        // Agregar clases específicas para elementos con submenús
        if ($has_children) {
            $classes[] = 'menu-item-has-children';
            $classes[] = 'has-submenu';
        }

        // Remover clases de elemento activo si es un elemento padre y tiene hijos
        if ($has_children && $depth === 0) {
            $classes = array_diff($classes, ['current-menu-item', 'current_page_item', 'current-menu-parent', 'current-menu-ancestor']);
        }

        // Si es un elemento hijo y su padre tiene la misma URL, mantener solo la clase active en el hijo
        if ($depth > 0 && $item->url === $item->menu_item_parent_url) {
            $classes[] = 'current-menu-item';
        }
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= $indent . '<li' . $class_names . '>';

        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        
        // SEO: If href is "#", use javascript:void(0) to avoid "uncrawlable link" warning
        $href = !empty($item->url) ? $item->url : '';
        if ($href === '#' || empty($href)) {
            $atts['href'] = 'javascript:void(0)';
            $atts['role'] = 'button';
        } else {
            $atts['href'] = $href;
        }

        // Agregar clases de estilo consistentes con tu diseño
        $link_classes = 'text-sm font-bold italic block py-2 hover:text-rosado transition-colors duration-200';
        if ($has_children) {
            $link_classes .= ' submenu-toggle';
            $atts['data-href'] = $item->url; // Store actual URL for desktop
        }
        $atts['class'] = $link_classes;
        
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';

        // Agregar botón desplegable solo si tiene submenús
        if ($has_children) {
            $item_output .= '<button class="submenu-btn lg:hidden absolute right-0 top-2 p-2 hover:text-rosado transition-colors duration-200" aria-expanded="false" aria-label="Expandir submenú">
                <svg class="w-5 h-5 transform transition-transform submenu-icon" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M19 9l-7 7-7-7"/>
                </svg>
            </button>';
        }

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
