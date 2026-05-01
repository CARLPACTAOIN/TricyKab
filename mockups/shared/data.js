// Centralized static demo data for mockups (no backend).
window.TricyKabMockData = {
  passenger: {
    name: "Jane Doe",
    phone: "+63 912 345 6789",
  },
  driver: {
    name: "Mariano Ramos",
    toda: "Poblacion TODA",
    plate: "KAB-1234",
  },
  booking: {
    reference: "TK-9822",
    pickup: "Kabacan Public Market",
    destination: "USM Main Gate",
  },

  /* ── Multi-Offer Carousel Data (Part 1) ── */
  offers: [
    {
      id: 1,
      passengerName: "Maria Clara",
      initials: "MC",
      pickup: "Kabacan Public Market",
      destination: "USM Main Gate",
      pickupDistance: 1.2,
      fare: 35.00,
      rideType: "SHARED",
      estimatedDuration: "~13 min",
      totalDistance: 3.2,
    },
    {
      id: 2,
      passengerName: "Jose Rizal",
      initials: "JR",
      pickup: "Poblacion Terminal",
      destination: "Nongnongan Barangay Hall",
      pickupDistance: 0.8,
      fare: 45.00,
      rideType: "SHARED",
      estimatedDuration: "~18 min",
      totalDistance: 4.7,
    },
    {
      id: 3,
      passengerName: "Andres Bonifacio",
      initials: "AB",
      pickup: "Osias Elementary School",
      destination: "Kabacan Bus Terminal",
      pickupDistance: 2.1,
      fare: 95.00,
      rideType: "SPECIAL",
      estimatedDuration: "~22 min",
      totalDistance: 5.8,
    },
  ],

  /* ── Multi-Passenger Active Trip Data (Part 2) ── */
  activeTrip: {
    capacity: 4,
    passengers: [
      {
        id: 1,
        name: "Maria Clara",
        initials: "MC",
        pickup: "Kabacan Public Market",
        destination: "USM Main Gate",
        pickupNote: "Near the south gate entrance, beside the fruit stall.",
        fare: 35.00,
        status: "onboard",
      },
      {
        id: 2,
        name: "Jose Rizal",
        initials: "JR",
        pickup: "Poblacion Terminal",
        destination: "Nongnongan Barangay Hall",
        pickupNote: "Under the waiting shed, wearing a blue shirt.",
        fare: 45.00,
        status: "waiting",
      },
    ],
  },
};
