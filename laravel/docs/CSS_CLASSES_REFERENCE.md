# Serien-Kachelansicht - CSS-Klassen Referenz

## Quick Reference Guide

### 📦 Verwendete Klassen in den Templates

#### list-series-grid.blade.php

```html
<!-- View Toggle -->
<div class="series-view-toggle">
    <div class="series-view-toggle__buttons">
        <button class="series-view-toggle__button [--active]">
            <span class="series-view-toggle__button-text">Text</span>
        </button>
    </div>
</div>

<!-- Grid -->
<div class="series-grid">
    <!-- Empty State -->
    <div class="series-grid__empty">
        <div class="series-grid__empty-icon"></div>
        <h3 class="series-grid__empty-title"></h3>
        <p class="series-grid__empty-description"></p>
    </div>
</div>
```

#### series-card.blade.php

```html

<div class="series-card">
    <a class="series-card__link">
        <!-- Cover -->
        <div class="series-card__cover">
            <img class="series-card__cover-image">
            <!-- ODER -->
            <div class="series-card__placeholder">
                <p class="series-card__placeholder-text"></p>
            </div>

            <!-- Badge -->
            <div class="series-card__badge"></div>

            <!-- Progress -->
            <div class="series-card__progress">
                <div class="series-card__progress-fill"></div>
            </div>
        </div>

        <!-- Info -->
        <div class="series-card__info">
            <h3 class="series-card__title"></h3>
            <div class="series-card__meta">
                <span class="series-card__episodes"></span>
                <span class="series-card__percentage [--complete]"></span>
            </div>
        </div>
    </a>

    <!-- Overlay -->
    <div class="series-card__overlay">
        <a class="series-card__action"></a>
    </div>
</div>
```

---

## 🎨 Styling-Eigenschaften pro Klasse

### .series-card

```scss
position: relative
overflow: hidden
border-radius:

12
px
background: white

/
dark
box-shadow:

0
2
px

8
px

rgba
(
0
,
0
,
0
,
0.1
)
transition: all

0.3
s

cubic-bezier
(
0.4
,
0
,
0.2
,
1
)

&:hover {
    transform: translateY(-4px)
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15)
}
```

### .series-card__cover

```scss
position: relative
width:

100
%
padding-bottom:

150
% // 2:3 Aspect Ratio
overflow: hidden
background: gradient
```

### .series-card__cover-image

```scss
position: absolute
width:

100
%
height:

100
%
object-fit: cover
transition: transform

0.4
s
.series-card:hover & {
    transform: scale(1.08)
}
```

### .series-card__badge

```scss
position: absolute
top:

0.75
rem
right:

0.75
rem
width:

2
rem
height:

2
rem
border-radius:

50
%
background: #10b981

(
green

)
box-shadow:

0
2
px

8
px

rgba
(
16
,
185
,
129
,
0.4
)
z-index:

2
```

### .series-card__progress

```scss
position: absolute
bottom:

0
left:

0
right:

0
height:

6
px
background: #e5e7eb
z-index:

1
```

### .series-card__progress-fill

```scss
height:

100
%
background: #3b82f6

(
blue

)
transition: width

0.5
s ease
box-shadow:

0
0
10
px

rgba
(
59
,
130
,
246
,
0.5
)
```

### .series-card__overlay

```scss
position: absolute
inset:

0
display: flex
align-items: center
justify-content: center
gap:

0.75
rem
background:

rgba
(
0
,
0
,
0
,
0.65
)
opacity:

0
transition: opacity

0.3
s
z-index:

3

.series-card:hover & {
    opacity: 1
}
```

### .series-card__action

```scss
width:

2.5
rem
height:

2.5
rem
border-radius:

50
%
background: white
box-shadow:

0
4
px

12
px

rgba
(
0
,
0
,
0
,
0.2
)
transition: transform

0.3
s&:hover {
    transform: scale(1.15)
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3)
}
```

---

## 🎭 Modifiers & States

### Active State

```html

<button class="series-view-toggle__button series-view-toggle__button--active">
```

```scss
background: #3b82f6
color: white
box-shadow:

0
2
px

4
px

rgba
(
59
,
130
,
246
,
0.3
)
```

### Complete State

```html
<span class="series-card__percentage series-card__percentage--complete">
```

```scss
color: #10b981

(
green

)
```

### Loading State

```html

<div class="series-card series-card--loading">
```

```scss
animation: shimmer

1.5
s infinite
```

---

## 📐 Responsive Breakpoints

```scss
// Default (Mobile)
grid-template-columns:

repeat
(
2
,
1
fr

)

// Small (640px+)
@media (min-width: 640px) {
    grid-template-columns: repeat(3, 1fr)
}

// Medium (768px+)
@media (min-width: 768px) {
    grid-template-columns: repeat(4, 1fr)
    gap: 1.5rem
}

// Large (1024px+)
@media (min-width: 1024px) {
    grid-template-columns: repeat(5, 1fr)
}

// XL (1280px+)
@media (min-width: 1280px) {
    grid-template-columns: repeat(6, 1fr)
}

// 2XL (1536px+)
@media (min-width: 1536px) {
    grid-template-columns: repeat(7, 1fr)
}
```

---

## 🎬 Animationen

### Fade In (Cards)

```scss
@keyframes fadeIn {
    from {
        opacity: 0
        transform: translateY(10px)
    }
    to {
        opacity: 1
        transform: translateY(0)
    }
}

animation: fadeIn

0.4
s ease-out
```

### Shimmer (Loading)

```scss
@keyframes shimmer {
    0% {
        background-position: -200% 0
    }
    100% {
        background-position: 200% 0
    }
}

animation: shimmer

1.5
s infinite
```

---

## 🎨 CSS Variables Übersicht

### Layout

```scss
--series-card-radius:

12
px
--series-card-shadow:

0
2
px

8
px

rgba
(
0
,
0
,
0
,
0.1
)
--series-card-shadow-hover:

0
8
px

24
px

rgba
(
0
,
0
,
0
,
0.15
)
--series-transition: all

0.3
s

cubic-bezier
(
0.4
,
0
,
0.2
,
1
)
```

### Colors (Light)

```scss
--series-bg: #ffffff
--series-text-primary: #1f2937
--series-text-secondary: #6b7280
--series-border: #e5e7eb
--series-progress-bg: #e5e7eb
--series-progress-fill: #3b82f6
--series-completion-bg: #10b981
--series-overlay-bg:

rgba
(
0
,
0
,
0
,
0.65
)
```

### Colors (Dark)

```scss
--series-bg: #1f2937
--series-text-primary: #f9fafb
--series-text-secondary: #9ca3af
--series-border: #374151
--series-progress-bg: #374151
--series-progress-fill: #60a5fa
--series-completion-bg: #34d399
```

---

## 🔧 Anpassungsbeispiele

### Größere Karten-Rundung

```scss
:root {
    --series-card-radius: 20px;
}
```

### Andere Accent-Farbe

```scss
:root {
    --series-progress-fill: #8b5cf6; // Lila
    --series-completion-bg: #f59e0b; // Orange
}
```

### Langsamere Transitions

```scss
:root {
    --series-transition: all 0.6s ease-in-out;
}
```

### Mehr Hover-Lift

```scss
.series-card:hover {
    transform: translateY(-8px); // Statt -4px
}
```

---

## 📝 Cheatsheet für neue Elemente

### Neues Element hinzufügen

```scss
// In _series.scss
.series-card {
    &__new-element {
        // Styling hier
    }
}
```

### In Template verwenden

```html

<div class="series-card__new-element">
    Inhalt
</div>
```

---

**Tipp:** Nutze die Browser DevTools, um live mit den CSS-Variablen zu experimentieren! 🎨

