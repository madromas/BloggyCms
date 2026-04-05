<?php

/**
* Менеджер для управления хлебными крошками в системе
*/
class BreadcrumbsManager {
    
    private $items = [];
    private $db;
    
    /**
    * Конструктор BreadcrumbsManager
    * @param mixed $db Подключение к базе данных
    */
    public function __construct($db = null) {
        $this->db = $db;
    }
    
    /**
    * Добавляет элемент в хлебные крошки
    * @param string $title Название элемента
    * @param string|null $url URL элемента (null для текущего элемента)
    * @return self
    */
    public function add($title, $url = null, $maxLength = 50) {
        if ($url === null && mb_strlen($title) > $maxLength) {
            $title = mb_substr($title, 0, $maxLength) . '...';
        }
        
        $this->items[] = [
            'title' => $title,
            'url' => $url
        ];
        return $this;
    }
    
    /**
    * Добавляет элемент в начало хлебных крошек
    * @param string $title Название элемента
    * @param string|null $url URL элемента
    * @return self
    */
    public function prepend($title, $url = null) {
        array_unshift($this->items, [
            'title' => $title,
            'url' => $url
        ]);
        return $this;
    }
    
    /**
    * Очищает все хлебные крошки
    * @return self
    */
    public function clear() {
        $this->items = [];
        return $this;
    }
    
    /**
    * Возвращает все элементы
    * @return array
    */
    public function getAll() {
        return $this->items;
    }
    
    /**
    * Проверяет, пусты ли хлебные крошки
    * @return bool
    */
    public function isEmpty() {
        return empty($this->items);
    }
    
    /**
    * Возвращает количество элементов
    * @return int
    */
    public function count() {
        return count($this->items);
    }
    
    /**
    * Возвращает последний элемент (текущую страницу)
    * @return array|null
    */
    public function getLast() {
        return !empty($this->items) ? end($this->items) : null;
    }
    
    /**
    * Рендерит хлебные крошки в HTML
    * @param array $options Опции отображения
    * @return string
    */
    public function render($options = []) {
        if (empty($this->items)) {
            return '';
        }
        
        $options = array_merge([
            'container_tag' => 'nav',
            'container_class' => 'breadcrumbs',
            'container_attributes' => ['aria-label' => 'breadcrumb'],
            'list_tag' => 'ol',
            'list_class' => 'breadcrumb',
            'item_tag' => 'li',
            'item_class' => 'breadcrumb-item',
            'active_class' => 'active',
            'separator' => null,
            'home_icon' => null,
            'home_icon_set' => 'bs',
            'schema' => true,
        ], $options);
        
        $html = '<' . $options['container_tag'] . ' class="' . $options['container_class'] . '"';
        
        foreach ($options['container_attributes'] as $attr => $value) {
            $html .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
        }
        $html .= '>';
        
        $html .= '<' . $options['list_tag'] . ' class="' . $options['list_class'] . '">';
        
        $total = count($this->items);
        foreach ($this->items as $index => $item) {
            $isLast = $index === $total - 1;
            $classes = $options['item_class'];
            
            if ($isLast) {
                $classes .= ' ' . $options['active_class'];
            }
            
            $html .= '<' . $options['item_tag'] . ' class="' . $classes . '"';
            
            if ($options['schema']) {
                $html .= ' itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement"';
            }
            
            $html .= '>';
            
            $iconHtml = '';
            if ($index === 0 && $options['home_icon']) {
                $iconHtml = bloggy_icon($options['home_icon_set'], $options['home_icon'], '18', 'currentColor', 'me-1 pb-1');
            }
            
            if (!$isLast && !empty($item['url'])) {
                $html .= '<a href="' . htmlspecialchars($item['url']) . '"';
                
                if ($options['schema']) {
                    $html .= ' itemprop="item"';
                }
                
                $html .= '>';
                $html .= $iconHtml;
                
                if ($options['schema']) {
                    $html .= '<span itemprop="name">' . htmlspecialchars($item['title']) . '</span>';
                } else {
                    $html .= htmlspecialchars($item['title']);
                }
                
                $html .= '</a>';
                
                if ($options['schema']) {
                    $html .= '<meta itemprop="position" content="' . ($index + 1) . '" />';
                }
            } else {
                if ($options['schema']) {
                    $html .= $iconHtml;
                    $html .= '<span itemprop="name">' . htmlspecialchars($item['title']) . '</span>';
                    $html .= '<meta itemprop="position" content="' . ($index + 1) . '" />';
                } else {
                    $html .= $iconHtml . htmlspecialchars($item['title']);
                }
            }
            
            $html .= '</' . $options['item_tag'] . '>';
        }
        
        $html .= '</' . $options['list_tag'] . '>';
        $html .= '</' . $options['container_tag'] . '>';
        
        return $html;
    }
    
    /**
    * Возвращает массив для JSON
    * @return array
    */
    public function toArray() {
        return $this->items;
    }
    
    /**
    * Возвращает JSON представление
    * @return string
    */
    public function toJson() {
        return json_encode($this->items, JSON_UNESCAPED_UNICODE);
    }
}