<style>
    .pi-shell {
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }

    .pi-hero {
        background:
            radial-gradient(circle at top right, rgba(18, 186, 255, .16), transparent 32%),
            linear-gradient(135deg, #0f1724, #10283c 58%, #0f1c2b);
        border: 1px solid rgba(94, 141, 182, .2);
        border-radius: 1rem;
        box-shadow: 0 20px 48px rgba(0, 0, 0, .18);
        overflow: hidden;
    }

    .pi-hero__body {
        padding: 1.35rem 1.45rem;
    }

    .pi-kicker {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .74rem;
        text-transform: uppercase;
        letter-spacing: .16em;
        color: #5ad8ff;
        margin-bottom: .75rem;
    }

    .pi-title {
        font-size: 1.9rem;
        line-height: 1.08;
        font-weight: 700;
        margin: 0;
        color: #f4fbff;
    }

    .pi-subtitle {
        margin: .6rem 0 0;
        max-width: 66rem;
        color: rgba(217, 232, 243, .76);
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

    .pi-panel--dark {
        background:
            linear-gradient(180deg, rgba(17, 27, 39, .98), rgba(14, 22, 34, .98));
        color: #edf6fb;
        border: 1px solid rgba(91, 128, 159, .18);
        box-shadow: 0 14px 36px rgba(0, 0, 0, .18);
    }

    .pi-panel__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .8rem;
        padding: 1rem 1.2rem;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .pi-panel--dark .pi-panel__header {
        border-bottom-color: rgba(112, 145, 175, .14);
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

    .pi-panel--dark .pi-panel__subtitle,
    .pi-panel--dark .pi-muted {
        color: rgba(211, 226, 238, .72) !important;
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

    .pi-stepper {
        display: grid;
        gap: .8rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .pi-stepper__item {
        display: flex;
        gap: .8rem;
        align-items: flex-start;
        padding: .95rem 1rem;
        border-radius: .95rem;
        border: 1px solid rgba(15, 23, 42, .08);
        background: rgba(248, 250, 252, .94);
    }

    .pi-stepper__badge {
        width: 2rem;
        height: 2rem;
        flex: 0 0 2rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #0c4a6e;
        background: rgba(8, 145, 178, .14);
        border: 1px solid rgba(8, 145, 178, .24);
    }

    .pi-stepper__title {
        margin: 0;
        font-size: .97rem;
        font-weight: 700;
        color: #0f172a;
    }

    .pi-stepper__text {
        margin: .2rem 0 0;
        color: #64748b;
        font-size: .9rem;
    }

    @media (max-width: 1199px) {
        .pi-grid--two {
            grid-template-columns: 1fr;
        }
    }
</style>
