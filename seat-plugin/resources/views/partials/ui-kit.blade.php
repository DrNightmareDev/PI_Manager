<style>
    .pi-shell {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .pi-hero {
        background: linear-gradient(135deg, rgba(17, 24, 39, .96), rgba(9, 39, 58, .96));
        border: 1px solid rgba(55, 100, 140, .28);
        border-radius: 1rem;
        box-shadow: 0 18px 40px rgba(0, 0, 0, .16);
        overflow: hidden;
    }

    .pi-hero__body {
        padding: 1.25rem 1.35rem;
    }

    .pi-kicker {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .14em;
        color: #60d6ff;
        margin-bottom: .75rem;
    }

    .pi-title {
        font-size: 1.9rem;
        line-height: 1.1;
        font-weight: 700;
        margin: 0;
        color: #f5fbff;
    }

    .pi-subtitle {
        margin: .55rem 0 0;
        color: rgba(212, 230, 242, .78);
        max-width: 68rem;
    }

    .pi-grid {
        display: grid;
        gap: 1rem;
    }

    .pi-grid--two {
        grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr);
    }

    .pi-grid--stats {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .pi-panel {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 1rem;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .pi-panel__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid rgba(15, 23, 42, .06);
        background: linear-gradient(180deg, rgba(248, 251, 253, 1), rgba(244, 247, 250, 1));
    }

    .pi-panel__title {
        display: flex;
        align-items: center;
        gap: .6rem;
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #112233;
    }

    .pi-panel__body {
        padding: 1rem 1.15rem 1.15rem;
    }

    .pi-stat {
        background: linear-gradient(180deg, #fff, #f8fbfd);
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: .95rem;
        padding: 1rem 1rem .9rem;
        min-height: 120px;
    }

    .pi-stat__label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .09em;
        color: #6b7d90;
        margin-bottom: .4rem;
    }

    .pi-stat__value {
        font-size: 1.9rem;
        line-height: 1.05;
        font-weight: 700;
        color: #122638;
    }

    .pi-stat__meta {
        font-size: .84rem;
        color: #6b7d90;
        margin-top: .45rem;
    }

    .pi-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .pi-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .38rem .72rem;
        border-radius: 999px;
        border: 1px solid rgba(15, 23, 42, .09);
        background: #f7fafc;
        color: #1d3448;
        font-size: .82rem;
        font-weight: 600;
        text-decoration: none;
    }

    .pi-chip--soft {
        background: rgba(96, 214, 255, .12);
        border-color: rgba(96, 214, 255, .22);
        color: #0f6d8f;
    }

    .pi-flow {
        display: grid;
        gap: .9rem;
    }

    .pi-flow__step {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: .9rem;
        padding: .9rem 1rem;
        background: linear-gradient(180deg, #fff, #fbfdff);
    }

    .pi-flow__step-title {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7d90;
        margin-bottom: .35rem;
    }

    .pi-flow__step strong {
        display: block;
        font-size: 1rem;
        color: #122638;
        margin-bottom: .25rem;
    }

    .pi-note {
        color: #6b7d90;
        font-size: .86rem;
        margin: 0;
    }

    .pi-pill-toggle {
        padding: .42rem .8rem;
        border-radius: 999px;
        border: 1px solid rgba(15, 23, 42, .14);
        background: #fff;
        color: #203549;
        font-weight: 600;
        font-size: .82rem;
        text-decoration: none;
    }

    .pi-pill-toggle.is-active,
    .pi-pill-toggle:hover {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    .pi-list-card {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: .95rem;
        background: #fff;
        padding: 1rem;
    }

    .pi-list-card + .pi-list-card {
        margin-top: .85rem;
    }

    .pi-muted {
        color: #6b7d90;
    }

    .pi-table-wrap table th {
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7d90;
        white-space: nowrap;
    }

    .pi-table-wrap table td {
        vertical-align: middle;
    }

    @media (max-width: 992px) {
        .pi-grid--two {
            grid-template-columns: 1fr;
        }
    }
</style>
