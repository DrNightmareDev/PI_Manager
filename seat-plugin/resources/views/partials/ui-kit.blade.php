<style>
    .pi-shell {
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }

    .pi-grid {
        display: grid;
        gap: 1rem;
    }

    .pi-grid--two {
        grid-template-columns: minmax(0, 1.3fr) minmax(320px, .7fr);
    }

    .pi-grid--stats {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .pi-grid--three {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .pi-panel {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 1rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .pi-panel__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .8rem;
        padding: 1rem 1.2rem;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .pi-panel__body {
        padding: 1.1rem 1.2rem;
    }

    .pi-panel__title {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
    }

    .pi-panel__subtitle {
        margin: .25rem 0 0;
        font-size: .92rem;
        color: #64748b;
    }

    .pi-stat {
        background: linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(246, 250, 253, .96));
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: .9rem;
        padding: 1rem 1.05rem;
        min-height: 124px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .8);
    }

    .pi-stat__label {
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .14em;
        color: #64748b;
    }

    .pi-stat__value {
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1.05;
        color: #0f172a;
    }

    .pi-stat__meta {
        color: #64748b;
        font-size: .92rem;
    }

    .pi-stat__group {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .pi-stack {
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }

    .pi-inline-form {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: end;
    }

    .pi-inline-form__grow {
        flex: 1 1 280px;
    }

    .pi-inline-form__narrow {
        flex: 0 1 220px;
    }

    .pi-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .pi-chip {
        display: inline-flex;
        align-items: center;
        gap: .38rem;
        padding: .34rem .68rem;
        border-radius: 999px;
        border: 1px solid rgba(37, 99, 235, .18);
        background: rgba(37, 99, 235, .08);
        color: #1d4ed8;
        font-size: .86rem;
        font-weight: 600;
        text-decoration: none;
    }

    .pi-chip--soft {
        background: rgba(15, 23, 42, .05);
        color: #475569;
        border-color: rgba(15, 23, 42, .08);
    }

    .pi-pill-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: .5rem .88rem;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 600;
        color: #52647b;
        background: #f7fafc;
        border: 1px solid rgba(71, 85, 105, .14);
        transition: all .12s ease;
    }

    .pi-pill-toggle:hover {
        color: #0f172a;
        border-color: rgba(37, 99, 235, .25);
    }

    .pi-pill-toggle.is-active {
        color: #0c4a6e;
        background: rgba(8, 145, 178, .14);
        border-color: rgba(8, 145, 178, .28);
        box-shadow: inset 0 0 0 1px rgba(8, 145, 178, .06);
    }

    /* Status-specific pill colors */
    .pi-pill-toggle--active.is-active {
        color: #166534;
        background: rgba(22, 163, 74, .1);
        border-color: rgba(22, 163, 74, .28);
    }

    .pi-pill-toggle--expired.is-active {
        color: #991b1b;
        background: rgba(220, 38, 38, .1);
        border-color: rgba(220, 38, 38, .28);
    }

    .pi-pill-toggle--stalled.is-active {
        color: #92400e;
        background: rgba(217, 119, 6, .1);
        border-color: rgba(217, 119, 6, .28);
    }

    /* Count badge inside a pill */
    .pi-pill-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.35rem;
        height: 1.35rem;
        padding: 0 .32rem;
        border-radius: 999px;
        font-size: .77rem;
        font-weight: 700;
        background: rgba(0, 0, 0, .1);
        margin-left: .32rem;
        line-height: 1;
    }

    .pi-pill-toggle--active.is-active .pi-pill-count { background: rgba(22, 163, 74, .2); }
    .pi-pill-toggle--expired.is-active .pi-pill-count { background: rgba(220, 38, 38, .2); }
    .pi-pill-toggle--stalled.is-active .pi-pill-count { background: rgba(217, 119, 6, .2); }

    .pi-note {
        background: rgba(15, 23, 42, .04);
        border: 1px solid rgba(15, 23, 42, .05);
        border-radius: .85rem;
        padding: .8rem .95rem;
        color: #475569;
    }

    .pi-flow {
        display: flex;
        flex-direction: column;
        gap: .8rem;
    }

    .pi-flow__step {
        padding: .9rem 1rem;
        background: rgba(248, 250, 252, .92);
        border-radius: .9rem;
        border: 1px solid rgba(15, 23, 42, .08);
    }

    .pi-flow__step-title {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: #64748b;
        margin-bottom: .35rem;
    }

    .pi-list-card {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: .9rem;
        padding: .95rem 1rem;
        background: rgba(255, 255, 255, .94);
    }

    .pi-list-card--active {
        border-color: rgba(37, 99, 235, .32);
        box-shadow: inset 0 0 0 1px rgba(37, 99, 235, .08);
    }

    .pi-table-wrap table th {
        white-space: nowrap;
        font-size: .76rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .pi-table-wrap table td {
        vertical-align: middle;
    }

    .pi-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: .7rem;
        align-items: center;
        justify-content: space-between;
    }

    .pi-toolbar__group {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
        align-items: center;
    }

    .pi-empty {
        padding: 1.4rem;
        border-radius: .95rem;
        border: 1px dashed rgba(100, 116, 139, .28);
        background: rgba(248, 250, 252, .8);
        color: #64748b;
        text-align: center;
    }

    .pi-muted {
        color: #64748b;
    }

    .pi-metric-line {
        display: flex;
        justify-content: space-between;
        gap: .8rem;
        font-size: .92rem;
        color: #475569;
    }

    /* Compact horizontal filter bar */
    .pi-filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .6rem;
    }

    .pi-filter-bar__select {
        width: auto;
        min-width: 148px;
        max-width: 210px;
        font-size: .88rem;
        height: 30px;
        padding-top: .2rem;
        padding-bottom: .2rem;
    }

    .pi-filter-bar__input {
        flex: 1 1 180px;
        max-width: 260px;
        font-size: .88rem;
        height: 30px;
        padding-top: .2rem;
        padding-bottom: .2rem;
    }

    .pi-filter-bar__sep {
        width: 1px;
        height: 1.4rem;
        background: rgba(15, 23, 42, .1);
        flex-shrink: 0;
        align-self: center;
    }

    .pi-filter-bar__group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .4rem;
    }

    .pi-panel__body--slim {
        padding: .7rem 1.2rem;
    }

    /* Small pill variant (used inside filter bar) */
    .pi-pill-toggle--sm {
        min-height: 30px;
        padding: .2rem .62rem;
        font-size: .84rem;
    }

    .pi-pill-toggle--sm .pi-pill-count {
        height: 1.1rem;
        min-width: 1.1rem;
        font-size: .71rem;
        margin-left: .26rem;
    }

    /* Collapsible panel header */
    .pi-panel__header--toggle {
        cursor: pointer;
        user-select: none;
    }

    .pi-panel__header--toggle:hover {
        background: rgba(15, 23, 42, .03);
    }

    .pi-panel__header--toggle[aria-expanded="false"] {
        border-bottom: none;
    }

    .pi-panel__chevron {
        flex-shrink: 0;
        color: #94a3b8;
        transition: transform .18s ease;
    }

    .pi-panel__header--toggle[aria-expanded="true"] .pi-panel__chevron {
        transform: rotate(180deg);
    }

    /* Collapsible list-card header (used in catalog tier accordion) */
    .pi-list-card--toggle {
        cursor: pointer;
        user-select: none;
        border-bottom: 1px solid rgba(15, 23, 42, .07);
        border-radius: 0;
        margin: 0;
        padding: .75rem 1rem;
        background: transparent;
    }

    .pi-list-card--toggle:first-child {
        border-radius: .9rem .9rem 0 0;
    }

    .pi-list-card--toggle[aria-expanded="false"] {
        border-bottom: none;
        border-radius: .9rem;
    }

    .pi-list-card--toggle:hover {
        background: rgba(15, 23, 42, .02);
    }

    .pi-accordion {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: .9rem;
        overflow: hidden;
    }

    .pi-accordion__body {
        padding: .75rem 1rem 1rem;
    }

    /* Large inline value (e.g. status count) */
    .pi-value-lg {
        display: block;
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1.1;
        color: #0f172a;
    }

    /* Empty state — centered */
    .pi-empty {
        text-align: center;
    }

    /* Chip hover for <a> elements */
    a.pi-chip:hover {
        background: rgba(37, 99, 235, .14);
        border-color: rgba(37, 99, 235, .3);
        color: #1d4ed8;
    }

    @media (max-width: 1199px) {
        .pi-grid--two {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .pi-stepper {
            grid-template-columns: 1fr;
        }

        .pi-grid--stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .pi-grid--three {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
