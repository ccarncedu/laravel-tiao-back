public function up()
{
    Schema::create('links', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('url');
        $table->boolean('approved')->default(false);
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}
