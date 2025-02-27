wp.blocks.registerBlockType("custom/booking-form", {
    title: "Medi Booking",
    icon: "calendar",
    category: "common",
    attributes: {},

    edit: () => {
        return wp.element.createElement(
            "div",
            { className: "medi-booking-block-placeholder" },
            "🔄 Medi Booking Block Content wird dynamisch geladen..."
        );
    },

    save: () => {
        return null;
    },
});
